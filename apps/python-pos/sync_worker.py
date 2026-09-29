import time
import requests
from PySide6.QtCore import QThread, Signal
from database.models import init_db, Product, Category
from sqlalchemy.orm import Session

class SyncWorker(QThread):
    # Signals to communicate with the main GUI thread
    sync_started = Signal()
    sync_finished = Signal(bool, str) # success, message
    products_updated = Signal()

    def __init__(self, db_path, api_url, pos_token):
        super().__init__()
        self.db_path = db_path
        self.api_url = api_url.rstrip('/')
        self.pos_token = pos_token
        self.is_running = True

    def run(self):
        while self.is_running:
            try:
                self.sync_started.emit()
                if self.is_running:
                    changed = self._sync_products()
                    self._sync_receipts()
                    if changed:
                        self.products_updated.emit()
                    self.sync_finished.emit(True, "Sync successful")
            except Exception as e:
                import traceback
                with open("sync_error.log", "a", encoding="utf-8") as f:
                    f.write(traceback.format_exc() + "\n")
                if self.is_running:
                    self.sync_finished.emit(False, str(e))
            
            # Sync every 10 seconds
            for _ in range(10):
                if not self.is_running:
                    break
                time.sleep(1)

    def _sync_products(self):
        headers = {
            'Authorization': f'Bearer {self.pos_token}',
            'Accept': 'application/json'
        }
        
        session = init_db(f'sqlite:///{self.db_path}')
        changed = False
        
        try:
            from database.models import Product, Category, PosReceiptItem
            
            # 1. PUSH OFFLINE PRODUCTS FIRST
            offline_products = session.query(Product).filter(Product.id < 0).all()
            for prod in offline_products:
                prod_payload = {
                    'name_th': prod.name_th,
                    'default_price': prod.default_price,
                    'barcode': prod.barcode or prod.sku_code
                }
                try:
                    resp = requests.post(f'{self.api_url}/api/pos/products', json=prod_payload, headers=headers, timeout=30)
                    if resp.status_code == 200:
                        new_id = resp.json().get('product_id')
                        if new_id:
                            old_id = prod.id
                            session.query(PosReceiptItem).filter(PosReceiptItem.product_id == old_id).update({"product_id": new_id}, synchronize_session=False)
                            session.query(Product).filter(Product.id == old_id).update({"id": new_id}, synchronize_session=False)
                            session.commit()
                            changed = True
                except Exception as e:
                    pass
            
            # 2. FETCH FROM SERVER
            response = requests.get(f'{self.api_url}/api/pos/products?all=1', headers=headers, timeout=45)
            response.raise_for_status()
            
            products_data = response.json()
            
            import json, hashlib
            products_json_str = json.dumps(products_data, sort_keys=True)
            current_hash = hashlib.md5(products_json_str.encode('utf-8')).hexdigest()
            if getattr(self, '_last_products_hash', None) == current_hash and not changed:
                return False
            self._last_products_hash = current_hash
            changed = True
            
            # 3. REPLACE LOCAL REPLICA
            session.query(Product).filter(Product.id > 0).delete(synchronize_session=False)
            
            for prod_data in products_data:
                prod = Product(
                    id=prod_data['id'],
                    sku_code=prod_data['sku_code'],
                    name_th=prod_data['name_th'],
                    name_en=prod_data.get('name_en', ''),
                    default_price=prod_data['default_price'],
                    pos_price=prod_data.get('pos_price', prod_data['default_price']),
                    category_id=prod_data.get('product_category_id'),
                    image_url=prod_data.get('image_url'),
                    is_active=prod_data.get('is_active', True),
                    barcode=prod_data.get('barcodes', [{}])[0].get('barcode', '') if prod_data.get('barcodes') else prod_data.get('sku_code', ''),
                    stock_qty=prod_data.get('stock_qty')
                )
                session.add(prod)
                
            session.commit()
            return changed
        except Exception as e:
            session.rollback()
            raise e
        finally:
            session.close()

    def _sync_receipts(self):
        headers = {
            'Authorization': f'Bearer {self.pos_token}',
            'Accept': 'application/json'
        }
        
        # Get active shift
        shift_response = requests.get(f'{self.api_url}/api/pos/shift', headers=headers, timeout=30)
        shift_response.raise_for_status()
        shift_data = shift_response.json().get('shift')
        
        if not shift_data:
            return
            
        shift_id = shift_data['id']
        branch_id = shift_data['branch_id']
        cashier_id = shift_data['cashier_id']
        
        session = init_db(f'sqlite:///{self.db_path}')
        from database.models import PosReceipt, PosReceiptItem, Product
        
        try:
            pending_receipts = session.query(PosReceipt).filter(PosReceipt.sync_status.in_(['pending', 'error'])).all()
            for receipt in pending_receipts:
                items = session.query(PosReceiptItem).filter_by(receipt_id=receipt.id).all()
                
                # Sync any offline products in this receipt BEFORE sending receipt payload
                for item in items:
                    if item.product_id < 0:
                        try:
                            prod = session.query(Product).filter_by(id=item.product_id).first()
                            prod_payload = {
                                'name_th': prod.name_th if prod else item.name,
                                'default_price': prod.default_price if prod else item.unit_price,
                                'barcode': (prod.barcode or prod.sku_code) if prod else item.sku_code
                            }
                            resp = requests.post(f'{self.api_url}/api/pos/products', json=prod_payload, headers=headers, timeout=30)
                            if resp.status_code == 200:
                                new_id = resp.json().get('product_id')
                                if new_id:
                                    old_id = item.product_id
                                    item.product_id = new_id
                                    session.commit()
                                    session.query(Product).filter_by(id=old_id).update({"id": new_id}, synchronize_session=False)
                                    session.commit()
                        except Exception as e:
                            pass
                
                payload = {
                    "branch_id": branch_id,
                    "shift_id": shift_id,
                    "cashier_id": cashier_id,
                    "method": receipt.payment_method,
                    "payment_confirmed": True,
                    "cash_received": receipt.received_amount,
                    "cash_amount": getattr(receipt, 'cash_amount', receipt.total_amount if receipt.payment_method == 'cash' else 0),
                    "transfer_amount": getattr(receipt, 'transfer_amount', receipt.total_amount if receipt.payment_method == 'transfer' else 0),
                    "change_amount": max(0, receipt.received_amount - getattr(receipt, 'cash_amount', receipt.total_amount if receipt.payment_method == 'cash' else 0)) if receipt.payment_method in ('cash', 'mixed') else 0,
                    "is_full_tax": getattr(receipt, 'is_full_tax', False),
                    "customer_name": getattr(receipt, 'customer_name', None),
                    "customer_tax_id": getattr(receipt, 'customer_tax_id', None),
                    "customer_address": getattr(receipt, 'customer_address', None),
                    "items": [
                        {
                            "product_id": item.product_id,
                            "qty": item.qty,
                            "unit_price": item.unit_price
                        } for item in items
                    ]
                }
                
                headers['Idempotency-Key'] = f"offline-receipt-{receipt.id}"
                
                checkout_response = requests.post(f'{self.api_url}/api/pos/checkout', json=payload, headers=headers, timeout=45)
                
                if checkout_response.status_code in (200, 201):
                    receipt.sync_status = 'synced'
                    resp_data = checkout_response.json()
                    if 'receipt_no' in resp_data:
                        receipt.doc_number = resp_data['receipt_no']
                elif checkout_response.status_code >= 400 and checkout_response.status_code < 500:
                    receipt.sync_status = 'error'
                    receipt.sync_error = checkout_response.text
                    
                session.commit()
        except Exception as e:
            session.rollback()
        finally:
            session.close()
