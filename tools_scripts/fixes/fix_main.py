import sys
import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Add self.customer_display = None
content = content.replace('self.current_shift = None', 'self.current_shift = None\n        self.customer_display = None')

# Add open CFD button in top bar
top_bar_code = '''        menu_btn = QPushButton("≡ เมนู")
        menu_btn.setStyleSheet("""'''
cfd_btn_code = '''        self.cfd_btn = QPushButton("จอฝั่งลูกค้า")
        self.cfd_btn.setStyleSheet("QPushButton { background-color: #8b5cf6; border: none; color: white; padding: 5px 15px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #7c3aed; }")
        self.cfd_btn.clicked.connect(self.toggle_customer_display)
        layout.addWidget(self.cfd_btn)
        
        menu_btn = QPushButton("≡ เมนู")
        menu_btn.setStyleSheet("""'''
content = content.replace(top_bar_code, cfd_btn_code)

# Add toggle_customer_display method before logout_cashier
toggle_cfd = '''    def toggle_customer_display(self):
        from ui.customer_display import CustomerDisplayWindow
        if not self.customer_display:
            self.customer_display = CustomerDisplayWindow(self)
        if self.customer_display.isHidden():
            self.customer_display.show()
        else:
            self.customer_display.hide()
            
    def logout_cashier(self):'''
content = content.replace('    def logout_cashier(self):', toggle_cfd)

# Update cart in update_cart_total
old_update_total = '''            self.cart_total_label.setText(f"฿ {total:,.2f}")'''
new_update_total = '''            self.cart_total_label.setText(f"฿ {total:,.2f}")
            if self.customer_display and not self.customer_display.isHidden():
                self.customer_display.update_cart(self.cart_table, total)'''
content = content.replace(old_update_total, new_update_total)

# Show payment in show_payment_dialog
old_dialog_exec = '''            dialog = PaymentDialog(self, total_amount, total_items, total_qty)
            if dialog.exec() == QDialog.Accepted:'''
new_dialog_exec = '''            dialog = PaymentDialog(self, total_amount, total_items, total_qty)
            if self.customer_display and not self.customer_display.isHidden():
                self.customer_display.show_payment("QR") # Default show QR
            if dialog.exec() == QDialog.Accepted:'''
content = content.replace(old_dialog_exec, new_dialog_exec)

# Show success in process_payment
old_print_receipt = '''            from ui.receipt_dialog import ReceiptDialog
            receipt = ReceiptDialog(self, receipt_data, self.config)'''
new_print_receipt = '''            if self.customer_display and not self.customer_display.isHidden():
                self.customer_display.show_success(change_amount)
                
            from ui.receipt_dialog import ReceiptDialog
            receipt = ReceiptDialog(self, receipt_data, self.config)'''
content = content.replace(old_print_receipt, new_print_receipt)

# Update version to v1.10.49
content = content.replace('v1.10.48', 'v1.10.49')

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done modifying main_window.py')
