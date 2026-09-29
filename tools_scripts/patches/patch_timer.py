import os

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add QTimer to imports
if "from PySide6.QtCore import Qt, QTimer" not in content:
    content = content.replace("from PySide6.QtCore import Qt", "from PySide6.QtCore import Qt, QTimer")

# Add idle timer to __init__
init_target = "self.customer_address = \"\""
init_replace = "self.customer_address = \"\"\n\n        self.clear_cart_requested = False\n        self.timer_seconds = 60\n        self.idle_timer = QTimer(self)\n        self.idle_timer.timeout.connect(self.prompt_idle)\n        self.idle_timer.start(1000)"

content = content.replace(init_target, init_replace)

# Add prompt_idle method
method_target = "def set_method(self, method):"
method_replace = """def prompt_idle(self):
        self.timer_seconds -= 1
        if self.timer_seconds <= 0:
            self.idle_timer.stop()
            msg = QMessageBox(self)
            msg.setWindowTitle("แจ้งเตือน")
            msg.setText("หมดเวลาชำระเงิน ต้องการทำรายการต่อหรือไม่?")
            btn_continue = msg.addButton("ยังทำรายการอยู่", QMessageBox.AcceptRole)
            btn_cancel = msg.addButton("ขึ้นบิลใหม่", QMessageBox.RejectRole)
            msg.exec()
            if msg.clickedButton() == btn_continue:
                self.timer_seconds = 60
                self.idle_timer.start(1000)
            else:
                self.clear_cart_requested = True
                self.reject()

    def set_method(self, method):"""

content = content.replace(method_target, method_replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Timer added!")
