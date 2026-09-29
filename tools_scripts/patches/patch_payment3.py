import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add show/hide checkbox in set_method
target = '''        elif method == "ผสม (Split)":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.show()
            self.received_str = "0"
            self.change_amount = 0
            self.update_displays()'''

replace = '''        elif method == "ผสม (Split)":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.show()
            if hasattr(self, 'show_split_qr_checkbox'):
                self.show_split_qr_checkbox.show()
            self.received_str = "0"
            self.change_amount = 0
            self.update_displays()'''

content = content.replace(target, replace)

target2 = '''        else:
            self.keypad_widget.show()
            self.input_field.show()
            self.split_frame.hide()
            self.received_str = ""
            self.update_displays()'''

replace2 = '''        else:
            self.keypad_widget.show()
            self.input_field.show()
            self.split_frame.hide()
            if hasattr(self, 'show_split_qr_checkbox'):
                self.show_split_qr_checkbox.hide()
                self.show_split_qr_checkbox.setChecked(False)
            self.received_str = ""
            self.update_displays()'''

content = content.replace(target2, replace2)

target3 = '''        if method == "QR":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.hide()'''

replace3 = '''        if method == "QR":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.hide()
            if hasattr(self, 'show_split_qr_checkbox'):
                self.show_split_qr_checkbox.hide()
                self.show_split_qr_checkbox.setChecked(False)'''

content = content.replace(target3, replace3)

# Timer fixes
timer_target = '''    def prompt_idle(self):
        self.timer_seconds -= 1
        if self.timer_seconds <= 0:'''

timer_replace = '''    def accept(self):
        if hasattr(self, 'idle_timer'):
            self.idle_timer.stop()
        super().accept()

    def reject(self):
        if hasattr(self, 'idle_timer'):
            self.idle_timer.stop()
        super().reject()

    def prompt_idle(self):
        self.timer_seconds -= 1
        parent_window = self.parent()
        if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():
            parent_window.customer_display.update_timer(self.timer_seconds)
            
        if self.timer_seconds <= 0:'''

content = content.replace(timer_target, timer_replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("payment_dialog patched!")
