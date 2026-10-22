import os
import time
import requests
import json
import uuid
from datetime import datetime
from PySide6.QtCore import QThread, Signal
from database.models import (
    init_db, Product, Category, Sale, SaleItem, Payment, Outbox, SyncLog
)
from sqlalchemy.orm import Session

class SyncWorker(QThread):
    # Signals to communicate with the main GUI thread
    sync_started = Signal()
    sync_finished = Signal(bool, str) # success, message
    products_updated = Signal()
    pending_count_updated = Signal(int) # emits number of unsynced bills for UI badge

    def __init__(self, db_path, api_url, pos_token):
        super().__init__()
        self.db_path = db_path
        self.api_url = api_url.rstrip('/') if api_url else 'http://localhost:8000'
        self.pos_token = pos_token
        self.is_running = True
        self.force_sync_flag = False

    def force_sync(self):
        self.force_sync_flag = True

    def _check_internet(self):
        # 1. First check if Local/Internal ERP API is reachable
        try:
            r = requests.get(f"{self.api_url}/api/pos/ping", timeout=2)
            if r.status_code == 200:
                return True
        except Exception:
            pass

        # 2. Check general external internet
        import socket
        try:
            socket.create_connection(("1.1.1.1", 53), timeout=1)
            return True
        except OSError:
            try:
                socket.create_connection(("8.8.8.8", 53), timeout=1)
                return True
            except OSError:
                return False

    def get_pending_count(self):
        session = init_db(f'sqlite:///{self.db_path}')
        try:
            return session.query(Sale).filter(Sale.status.in_(['pending', 'failed'])).count()
        except Exception:
            return 0
        finally:
            session.close()

    def _check_catalog_version(self):
        """
        ตรวจสอบเลขเวอร์ชันแคตตาล็อกราคา/โปรโมชั่นผ่าน /api/pos/ping
        ถ้ามีการแก้ไขราคา โปรโมชั่น หรือนาทีทองบน ERP ตัวเลขเวอร์ชันจะเปลี่ยนทันที
        """
        headers = {
            'Authorization': f'Bearer {self.pos_token}',
            'Accept': 'application/json'
        }
        try:
            resp = requests.get(f'{self.api_url}/api/pos/ping', headers=headers, timeout=5)
            if resp.status_code == 200:
                data = resp.json()
                server_ver = data.get('catalog_version', 0)
                last_ver = getattr(self, '_last_catalog_version', None)
                if last_ver is not None and server_ver != last_ver:
                    self._last_catalog_version = server_ver
                    return True
                self._last_catalog_version = server_ver
        except Exception:
            pass
        return False

    def run(self):
        loop_count = 0
        while self.is_running:
            try:
                # Update pending count badge on every loop
                pending_count = self.get_pending_count()
                self.pending_count_updated.emit(pending_count)

                if not self._check_internet():
                    raise Exception("No Internet Connection (ระบบออฟไลน์)")

                self.sync_started.emit()
                if self.is_running:
                    changed = False
                    
                    # เช็ค Real-time: มีการอัปเดตราคาหรือโปรโมชั่นบน ERP หรือไม่
                    version_changed = self._check_catalog_version()
                    
                    # Sync products ทันทีถ้าเวอร์ชันเปลี่ยน หรือรอบปกติ (ทุก ~30 วินาที)
                    if version_changed or (loop_count % 6 == 0):
                        changed = self._sync_products()
                    
                    # Sync outbox / sales ทุกรอบ (~5 วินาที)
                    self._sync_outbox_sales()
                    
                    if changed:
                        self.products_updated.emit()
                    self.sync_finished.emit(True, "Sync successful")
            except Exception as e:
                import traceback
                error_msg = str(e)
                log_dir = os.path.dirname(self.db_path)
                try:
                    with open(os.path.join(log_dir, "sync_error.log"), "a", encoding="utf-8") as f:
                        f.write(f"[{datetime.now().isoformat()}] {traceback.format_exc()}\n")
                except Exception:
                    pass
                if self.is_running:
                    self.sync_finished.emit(False, error_msg)
            
            loop_count += 1
            
            # รอบรอ 5 วินาที เพื่อให้ Real-time สูงสุด
            for _ in range(5):
                if not self.is_running:
                    break
                if getattr(self, 'force_sync_flag', False):
                    self.force_sync_flag = False
                    loop_count = -1
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
            # 1. PUSH OFFLINE PRODUCTS FIRST
            offline_products = session.query(Product).filter(Product.id < 0).all()
            for prod in offline_products:
                prod_payload = {
                    'name_th': prod.name_th,
                    'default_price': prod.default_price,
                    'barcode': str(prod.barcode or prod.sku_code)
                }
                try:
                    resp = requests.post(f'{self.api_url}/api/pos/products', json=prod_payload, headers=headers, timeout=30)
                    if resp.status_code == 200:
                        new_id = resp.json().get('product_id')
                        if new_id:
                            old_id = prod.id
                            session.query(SaleItem).filter(SaleItem.product_id == old_id).update({"product_id": new_id}, synchronize_session=False)
                            session.query(Product).filter(Product.id == old_id).update({"id": new_id}, synchronize_session=False)
                            session.commit()
                            changed = True
                except Exception:
                    pass
            
            # 2. FETCH FROM SERVER
            response = requests.get(f'{self.api_url}/api/pos/products?all=1', headers=headers, timeout=45)
            response.raise_for_status()
            
            products_data = response.json()
            
            import hashlib
            products_json_str = json.dumps(products_data, sort_keys=True)
            current_hash = hashlib.md5(products_json_str.encode('utf-8')).hexdigest()
            
            if getattr(self, '_last_products_hash', None) == current_hash and not changed:
                return False
            self._last_products_hash = current_hash
            changed = True
            
            # 3. REPLACE LOCAL REPLICA
            session.query(Product).filter(Product.id > 0).delete(synchronize_session=False)
            
            for prod_data in products_data:
                barcode_val = ''
                if prod_data.get('barcodes'):
                    barcode_val = str(prod_data.get('barcodes')[0].get('barcode', ''))
                else:
                    barcode_val = str(prod_data.get('sku_code', ''))

                prod = Product(
                    id=prod_data['id'],
                    sku_code=str(prod_data['sku_code']),
                    name_th=prod_data['name_th'],
                    name_en=prod_data.get('name_en', ''),
                    default_price=prod_data['default_price'],
                    pos_price=prod_data.get('pos_price', prod_data['default_price']),
                    category_id=prod_data.get('product_category_id'),
                    image_url=prod_data.get('image_url'),
                    is_active=prod_data.get('is_active', True),
                    barcode=barcode_val,
                    stock_qty=prod_data.get('stock_qty'),
                    is_scale_item=bool(prod_data.get('is_weight') or prod_data.get('is_scale_item') or False)
                )
                session.add(prod)
                
            session.commit()
            
            # Record in sync_logs
            log = SyncLog(
                direction='pull',
                sync_type='products',
                record_count=len(products_data),
                status='success',
                message=f'Synced {len(products_data)} products successfully'
            )
            session.add(log)
            session.commit()
            return changed
        except Exception as e:
            session.rollback()
            try:
                log = SyncLog(
                    direction='pull',
                    sync_type='products',
                    record_count=0,
                    status='failed',
                    message=str(e)
                )
                session.add(log)
                session.commit()
            except Exception:
                pass
            raise e
        finally:
            session.close()

    def _sync_outbox_sales(self):
        headers = {
            'Authorization': f'Bearer {self.pos_token}',
            'Accept': 'application/json'
        }
        
        # Get active shift from server
        try:
            shift_response = requests.get(f'{self.api_url}/api/pos/shift', headers=headers, timeout=30)
            shift_response.raise_for_status()
            shift_data = shift_response.json().get('shift')
        except Exception:
            return
        
        if not shift_data:
            return
            
        shift_id = shift_data['id']
        branch_id = shift_data['branch_id']
        cashier_id = shift_data['cashier_id']
        
        session = init_db(f'sqlite:///{self.db_path}')
        
        try:
            # 1. First ensure all pending sales have an Outbox entry
            pending_sales = session.query(Sale).filter(Sale.status.in_(['pending', 'failed'])).all()
            for sale in pending_sales:
                existing_outbox = session.query(Outbox).filter_by(aggregate_id=sale.local_sale_id).first()
                if not existing_outbox:
                    items = session.query(SaleItem).filter_by(sale_id=sale.local_sale_id).all()
                    payload = {
                        "local_sale_id": sale.local_sale_id,
                        "branch_id": branch_id,
                        "shift_id": shift_id,
                        "cashier_id": cashier_id,
                        "method": sale.payment_method,
                        "payment_confirmed": True,
                        "cash_received": sale.received_amount,
                        "cash_amount": sale.cash_amount or (sale.total_amount if sale.payment_method == 'cash' else 0),
                        "transfer_amount": sale.transfer_amount or (sale.total_amount if sale.payment_method == 'transfer' else 0),
                        "change_amount": max(0, sale.received_amount - (sale.cash_amount or sale.total_amount)) if sale.payment_method in ('cash', 'mixed') else 0,
                        "is_full_tax": sale.is_full_tax,
                        "customer_name": sale.customer_name,
                        "customer_tax_id": sale.customer_tax_id,
                        "customer_address": sale.customer_address,
                        "items": [
                            {
                                "product_id": it.product_id,
                                "qty": it.qty,
                                "unit_price": it.unit_price
                            } for it in items
                        ]
                    }
                    outbox_entry = Outbox(
                        event_type='sale_checkout',
                        aggregate_id=sale.local_sale_id,
                        payload_json=json.dumps(payload),
                        idempotency_key=sale.idempotency_key or f"offline-sale-{sale.local_sale_id}",
                        status='pending'
                    )
                    session.add(outbox_entry)
            session.commit()

            # 2. Process outbox queue
            pending_outbox = session.query(Outbox).filter(Outbox.status.in_(['pending', 'failed'])).all()
            for entry in pending_outbox:
                sale = session.query(Sale).filter_by(local_sale_id=entry.aggregate_id).first()
                payload = json.loads(entry.payload_json)
                
                # Ensure valid server shift, branch, and cashier IDs are used
                if branch_id:
                    payload['branch_id'] = branch_id
                if shift_id:
                    payload['shift_id'] = shift_id
                if cashier_id:
                    payload['cashier_id'] = cashier_id
                entry.payload_json = json.dumps(payload)

                req_headers = dict(headers)
                req_headers['Idempotency-Key'] = entry.idempotency_key
                
                try:
                    checkout_response = requests.post(f'{self.api_url}/api/pos/checkout', json=payload, headers=req_headers, timeout=45)
                    
                    if checkout_response.status_code in (200, 201):
                        entry.status = 'synced'
                        entry.synced_at = datetime.utcnow()
                        if sale:
                            sale.status = 'synced'
                            sale.synced_at = datetime.utcnow()
                            resp_data = checkout_response.json()
                            if 'receipt_no' in resp_data:
                                sale.doc_number = resp_data['receipt_no']
                                sale.server_receipt_id = resp_data.get('receipt_id') or resp_data['receipt_no']
                        
                        log = SyncLog(
                            direction='push',
                            sync_type='sale_checkout',
                            record_count=1,
                            status='success',
                            message=f"Sale {entry.aggregate_id} synced"
                        )
                        session.add(log)
                    elif 400 <= checkout_response.status_code < 500:
                        entry.status = 'failed'
                        entry.retry_count += 1
                        entry.last_error = checkout_response.text
                        if sale:
                            sale.status = 'failed'
                            sale.sync_error = checkout_response.text
                    else:
                        entry.retry_count += 1
                        entry.last_error = f"Server returned HTTP {checkout_response.status_code}"
                except Exception as ex:
                    entry.retry_count += 1
                    entry.last_error = str(ex)
                    
                session.commit()
                
            # Emit updated pending count to update badge
            updated_pending = session.query(Sale).filter(Sale.status.in_(['pending', 'failed'])).count()
            self.pending_count_updated.emit(updated_pending)

        except Exception as e:
            session.rollback()
        finally:
            session.close()
