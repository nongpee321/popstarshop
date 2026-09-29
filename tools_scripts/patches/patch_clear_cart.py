import os
import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """            if dialog.exec() == QDialog.Accepted:
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
                    } if dialog.is_full_tax else None
                )"""

replacement = """            if dialog.exec() == QDialog.Accepted:
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
                    } if dialog.is_full_tax else None
                )
            else:
                if getattr(dialog, 'clear_cart_requested', False):
                    self.cart_table.setRowCount(0)
                    self.update_cart_total()
                if self.customer_display and not self.customer_display.isHidden():
                    self.customer_display.update_cart(self.cart_table, self.total_amount)"""

content = content.replace(target, replacement)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Handled clear cart requested!")
