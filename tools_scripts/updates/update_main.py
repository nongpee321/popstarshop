import sys
import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Update calling process_payment
old_call = '''            if dialog.exec() == QDialog.Accepted:
                self.process_payment(dialog.selected_method, dialog.received_amount, dialog.change_amount)'''

new_call = '''            if dialog.exec() == QDialog.Accepted:
                self.process_payment(
                    method=dialog.selected_method,
                    received_amount=dialog.received_amount,
                    change_amount=dialog.change_amount,
                    cash_amount=dialog.cash_amount,
                    transfer_amount=dialog.transfer_amount,
                    is_full_tax=dialog.is_full_tax,
                    customer_info={
                        'name': dialog.customer_name,
                        'tax_id': dialog.customer_tax_id,
                        'address': dialog.customer_address
                    }
                )'''

content = content.replace(old_call, new_call)

# Update def process_payment
match = re.search(r'def process_payment\(self, method, received_amount, change_amount\):.*?session\.commit\(\)', content, re.DOTALL)
if match:
    old_proc = match.group(0)
    new_proc = '''def process_payment(self, method, received_amount, change_amount, cash_amount=0, transfer_amount=0, is_full_tax=False, customer_info=None):
        try:
            import uuid
            from datetime import datetime
            import json
            
            db_path = os.path.join(self.app_data_dir, 'pos_offline.db')
            session = init_db(f"sqlite:///{db_path}")
            
            receipt_id = str(uuid.uuid4())
            total_amount = 0.0
            
            method_map = {
                'เงินสด': 'cash',
                'QR': 'transfer',
                'ผสม (Split)': 'split',
                'บัตรเครดิต': 'card',
                'เช็ค': 'check'
            }
            
            if method == 'เงินสด':
                cash_amount = received_amount - change_amount
                transfer_amount = 0
            elif method == 'QR':
                transfer_amount = received_amount
                cash_amount = 0
                
            cust_name = customer_info.get('name') if customer_info else None
            cust_tax = customer_info.get('tax_id') if customer_info else None
            cust_addr = customer_info.get('address') if customer_info else None
            
            # Note: We will save total_amount below after loop
            
            items = []
            for row in range(self.cart_table.rowCount()):
                name_item = self.cart_table.item(row, 0)
                qty_item = self.cart_table.item(row, 1)
                price_item = self.cart_table.item(row, 2)
                
                if not name_item or not qty_item or not price_item:
                    continue
                    
                product_id = name_item.data(Qt.UserRole)
                qty = float(qty_item.text())
                price = float(price_item.text())
                total = qty * price
                total_amount += total
                
                items.append(PosReceiptItem(
                    receipt_id=receipt_id,
                    product_id=product_id,
                    sku_code='OFFLINE',
                    name=name_item.text(),
                    qty=qty,
                    unit_price=price,
                    total_price=total
                ))
                
            doc_no = 'OFFLINE-' + receipt_id[:8].upper()
            
            new_receipt = PosReceipt(
                id=receipt_id,
                shift_id=self.current_shift.get('local_id') if getattr(self, 'current_shift', None) else None,
                doc_number=doc_no,
                total_amount=total_amount,
                received_amount=received_amount,
                payment_method=method_map.get(method, 'cash'),
                cash_amount=cash_amount,
                transfer_amount=transfer_amount,
                is_full_tax=is_full_tax,
                customer_name=cust_name,
                customer_tax_id=cust_tax,
                customer_address=cust_addr,
                sync_status='pending'
            )
            
            session.add(new_receipt)
            session.add_all(items)
            session.commit()'''
            
    content = content.replace(old_proc, new_proc)
    with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Process payment updated.")
else:
    print("Could not find process_payment")

