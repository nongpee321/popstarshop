import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

old_block = '''            session.add(new_receipt)
            session.add_all(items)
            session.commit()
            session.close()

            self.cart_table.setRowCount(0)
            self.update_cart_total()

            # Show Receipt Dialog
            from ui.receipt_dialog import ReceiptDialog
            receipt_dialog = ReceiptDialog(self, receipt_data=receipt_data)'''

new_block = '''            session.add(new_receipt)
            session.add_all(items)
            session.commit()
            session.close()
            
            # Prepare receipt_data for the dialog/printer
            receipt_data = {
                'doc_number': doc_no,
                'date': datetime.utcnow().strftime('%d/%m/%Y %H:%M'),
                'branch': 'สำนักงานใหญ่',
                'cashier': self.config.get('cashier_name', 'Unknown'),
                'items': [{'name': item.name, 'qty': item.qty, 'price': item.unit_price, 'total': item.total_price} for item in items],
                'total': total_amount,
                'payment_method': method_map.get(method, 'cash'),
                'received': received_amount,
                'change': change_amount,
                'cash_amount': cash_amount,
                'transfer_amount': transfer_amount,
                'is_full_tax': is_full_tax,
                'customer_name': cust_name,
                'customer_tax_id': cust_tax,
                'customer_address': cust_addr
            }

            self.cart_table.setRowCount(0)
            self.update_cart_total()

            # Show Receipt Dialog
            from ui.receipt_dialog import ReceiptDialog
            receipt_dialog = ReceiptDialog(self, receipt_data=receipt_data)'''

content = content.replace(old_block, new_block)

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed receipt_data")
