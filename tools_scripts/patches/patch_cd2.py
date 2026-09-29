import os
import re

path = r'D:\pop-erp-food\python-pos\ui\customer_display.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''        self.qr_image.setScaledContents(True) # Ensure it fits exactly
        self.qr_image.setStyleSheet("background-color: white; padding: 15px; border-radius: 12px; border: 3px solid #007934;")
        qr_layout.addWidget(self.qr_image)
        
        right_panel.addWidget(self.qr_container)'''

replace = '''        self.qr_image.setScaledContents(True) # Ensure it fits exactly
        self.qr_image.setStyleSheet("background-color: white; padding: 15px; border-radius: 12px; border: 3px solid #007934;")
        qr_layout.addWidget(self.qr_image)
        
        self.qr_timer_lbl = QLabel("")
        self.qr_timer_lbl.setObjectName("statusLabel")
        self.qr_timer_lbl.setAlignment(Qt.AlignCenter)
        self.qr_timer_lbl.setStyleSheet("color: #E21B22; font-size: 24px; font-weight: bold;")
        qr_layout.addWidget(self.qr_timer_lbl)
        
        right_panel.addWidget(self.qr_container)'''

content = content.replace(target, replace)

method_target = '''    def show_payment(self, method="QR", override_amount=None):
        if method == "QR":'''

method_replace = '''    def update_timer(self, seconds):
        if seconds >= 0:
            self.qr_timer_lbl.setText(f"เวลาเหลือ: {seconds} วินาที")
        else:
            self.qr_timer_lbl.setText("")

    def show_payment(self, method="QR", override_amount=None):
        if method == "QR":'''

content = content.replace(method_target, method_replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("customer_display patched!")
