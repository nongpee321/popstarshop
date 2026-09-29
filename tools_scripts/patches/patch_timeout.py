import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

custom_dialog = '''
class IdleTimeoutDialog(QDialog):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setWindowTitle("แจ้งเตือนหมดเวลา")
        self.setFixedSize(400, 200)
        self.setStyleSheet("background-color: white; font-family: Tahoma, sans-serif;")
        
        layout = QVBoxLayout(self)
        
        self.lbl_msg = QLabel("หมดเวลาชำระเงิน ต้องการทำรายการต่อหรือไม่?\\nระบบจะยกเลิกบิลอัตโนมัติใน 60 วินาที")
        self.lbl_msg.setAlignment(Qt.AlignCenter)
        self.lbl_msg.setStyleSheet("font-size: 16px; font-weight: bold; color: #111827;")
        layout.addWidget(self.lbl_msg)
        
        btn_layout = QHBoxLayout()
        self.btn_continue = QPushButton("ยังทำรายการอยู่")
        self.btn_continue.setStyleSheet("background-color: #10b981; color: white; padding: 10px; border-radius: 8px; font-weight: bold; font-size: 16px;")
        self.btn_continue.clicked.connect(self.accept)
        
        self.btn_cancel = QPushButton("ขึ้นบิลใหม่")
        self.btn_cancel.setStyleSheet("background-color: #ef4444; color: white; padding: 10px; border-radius: 8px; font-weight: bold; font-size: 16px;")
        self.btn_cancel.clicked.connect(self.reject)
        
        btn_layout.addWidget(self.btn_continue)
        btn_layout.addWidget(self.btn_cancel)
        layout.addLayout(btn_layout)
        
        self.countdown = 60
        self.timer = QTimer(self)
        self.timer.timeout.connect(self.update_timer)
        self.timer.start(1000)
        
    def update_timer(self):
        self.countdown -= 1
        self.lbl_msg.setText(f"หมดเวลาชำระเงิน ต้องการทำรายการต่อหรือไม่?\\nระบบจะยกเลิกบิลอัตโนมัติใน {self.countdown} วินาที")
        if self.countdown <= 0:
            self.timer.stop()
            self.reject()
'''

# Find class PaymentDialog(QDialog):
content = content.replace("class PaymentDialog(QDialog):", custom_dialog + "\nclass PaymentDialog(QDialog):")

# Now update prompt_idle:
target_idle = r'''            msg = QMessageBox\(self\)
            msg\.setWindowTitle\(".*?"\)
            msg\.setText\(".*?"\)
            btn_continue = msg\.addButton\(".*?", QMessageBox\.AcceptRole\)
            btn_cancel = msg\.addButton\(".*?", QMessageBox\.RejectRole\)
            msg\.exec\(\)
            if msg\.clickedButton\(\) == btn_continue:'''

replace_idle = '''            dialog = IdleTimeoutDialog(self)
            if dialog.exec() == QDialog.Accepted:'''

content = re.sub(target_idle, replace_idle, content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Added IdleTimeoutDialog")
