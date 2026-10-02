import os
import requests
from PySide6.QtWidgets import (
    QDialog, QVBoxLayout, QHBoxLayout, QPushButton, QLabel, QLineEdit, QMessageBox, QWidget
)
from PySide6.QtCore import Qt
from PySide6.QtGui import QFont

class CashierLoginDialog(QDialog):
    def __init__(self, parent=None, api_url="", pos_token=""):
        super().__init__(parent)
        self.api_url = api_url.rstrip("/")
        self.pos_token = pos_token
        self.selected_cashier = None
        
        self.setWindowTitle("เข้าสู่ระบบแคชเชียร์")
        self.setFixedSize(400, 350)
        self.setStyleSheet("background-color: white; color: #111827; font-family: 'Segoe UI', sans-serif;")
        
        self.init_ui()
        
    def init_ui(self):
        layout = QVBoxLayout(self)
        layout.setContentsMargins(30, 30, 30, 30)
        layout.setSpacing(15)
        
        title = QLabel("เข้าสู่ระบบแคชเชียร์")
        title.setStyleSheet("font-size: 22px; font-weight: bold; color: #0f172a;")
        title.setAlignment(Qt.AlignCenter)
        layout.addWidget(title)
        
        lbl_cashier = QLabel("ชื่อผู้ใช้ / รหัสพนักงาน:")
        lbl_cashier.setStyleSheet("font-weight: bold;")
        layout.addWidget(lbl_cashier)
        
        self.code_input = QLineEdit()
        self.code_input.setStyleSheet("padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 16px;")
        layout.addWidget(self.code_input)
        self.code_input.returnPressed.connect(self.do_login)
        
        lbl_pin = QLabel("รหัสผ่าน (ERP):")
        lbl_pin.setStyleSheet("font-weight: bold;")
        layout.addWidget(lbl_pin)
        
        self.pin_input = QLineEdit()
        self.pin_input.setEchoMode(QLineEdit.Password)
        self.pin_input.setStyleSheet("padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 16px;")
        layout.addWidget(self.pin_input)
        self.pin_input.returnPressed.connect(self.do_login)
        
        layout.addStretch()
        
        btn_layout = QHBoxLayout()
        cancel_btn = QPushButton("ยกเลิก")
        cancel_btn.setStyleSheet("background: #f1f5f9; padding: 10px; border-radius: 6px;")
        cancel_btn.clicked.connect(self.reject)
        
        self.login_btn = QPushButton("เข้าสู่ระบบ")
        self.login_btn.setStyleSheet("background: #3b82f6; color: white; font-weight: bold; padding: 10px; border-radius: 6px;")
        self.login_btn.clicked.connect(self.do_login)
        
        btn_layout.addWidget(cancel_btn)
        btn_layout.addWidget(self.login_btn)
        layout.addLayout(btn_layout)
            
    def do_login(self):
        code = self.code_input.text().strip()
        if not code:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาระบุชื่อผู้ใช้")
            return
            
        pin = self.pin_input.text().strip()
        if not pin:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาระบุรหัสผ่าน")
            return
            
        self.login_btn.setText("กำลังเข้าสู่ระบบ...")
        self.login_btn.setEnabled(False)
        self.repaint() # update UI immediately
            
        try:
            res = requests.post(
                f"{self.api_url}/api/pos/cashier/login",
                headers={'Authorization': f"Bearer {self.pos_token}", 'Accept': 'application/json'},
                json={'code': code, 'pin': pin},
                timeout=5
            )
            res.encoding = 'utf-8'
            if res.status_code == 403:
                QMessageBox.warning(self, "Access Denied", "ผู้ใช้งานนี้ไม่มีสิทธิ์เข้าใช้ระบบ POS (POS Rights Required)")
            elif res.status_code in (200, 422, 401, 404):
                try:
                    data = res.json()
                except:
                    data = {}
                if res.status_code == 200 and data.get('success'):
                    self.selected_cashier = data.get('cashier', {})
                    if 'code' not in self.selected_cashier:
                        self.selected_cashier['code'] = code
                    self.entered_pin = pin
                    self.accept()
                else:
                    QMessageBox.warning(self, "Login Failed", data.get('message', 'เข้าสู่ระบบล้มเหลว (ตรวจสอบรหัสผ่าน)'))
            else:
                QMessageBox.warning(self, "Error", f"เกิดข้อผิดพลาดจากเซิร์ฟเวอร์ (รหัส {res.status_code})")

        except Exception as e:
            error_msg = str(e).replace(self.pos_token, '***TOKEN_HIDDEN***')
            reply = QMessageBox.question(self, "Offline Mode", f"ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้:\n{error_msg}\n\nต้องการเข้าสู่ระบบแบบออฟไลน์ด้วยรหัส {code} หรือไม่?", QMessageBox.Yes | QMessageBox.No)
            if reply == QMessageBox.Yes:
                self.entered_pin = pin
                self.selected_cashier = {'code': code, 'name': f"พนักงาน (ออฟไลน์: {code})"}
                self.accept()

        finally:
            self.login_btn.setText("เข้าสู่ระบบ")
            self.login_btn.setEnabled(True)
