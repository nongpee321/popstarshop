import os
import sys
import requests
from PySide6.QtWidgets import (
    QDialog, QVBoxLayout, QHBoxLayout, QPushButton, QLabel, QLineEdit, QMessageBox, QWidget, QFrame
)
from PySide6.QtCore import Qt, QTimer
from PySide6.QtGui import QFont, QIcon, QPixmap

VERSION_STRING = "1.15.0"

class PosTokenLoginDialog(QDialog):
    """
    หน้าต่างเข้าสู่ระบบจุดขาย (POS Terminal Authentication)
    ธีมสีแดง-ขาว (Signature Pop Star Red & White) คมชัด สวยงาม ทรงพลัง ระดับ Commercial POS
    """
    def __init__(self, parent=None, api_url="http://localhost:8000", default_token="", default_username=""):
        super().__init__(parent)
        self.api_url = (api_url or "http://localhost:8000").rstrip("/")
        self.token = default_token.strip()
        self.username = default_username.strip()
        self.verified_device = None
        self.branch_name = ""
        self.device_name = ""
        self.selected_cashier = None
        self.entered_pin = ""

        self.setWindowTitle(f"PopCentral POS — เข้าสู่ระบบ (v{VERSION_STRING})")
        self.setFixedSize(440, 540)
        self.setStyleSheet("""
            QDialog {
                background-color: #ffffff;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
        """)

        self.init_ui()

    def init_ui(self):
        layout = QVBoxLayout(self)
        layout.setContentsMargins(0, 0, 0, 0)
        layout.setSpacing(0)

        # 1. Top Red Header Bar
        top_header = QWidget()
        top_header.setStyleSheet("background-color: #dc2626; border-top-left-radius: 0px; border-top-right-radius: 0px;")
        top_layout = QVBoxLayout(top_header)
        top_layout.setContentsMargins(30, 24, 30, 22)
        top_layout.setSpacing(4)
        top_layout.setAlignment(Qt.AlignCenter)

        # Brand / Logo
        if getattr(sys, 'frozen', False):
            base_dir = os.path.dirname(sys.executable)
        else:
            base_dir = os.path.dirname(os.path.dirname(__file__))
        logo_path = os.path.join(base_dir, 'assets', 'logo.png')
        
        if os.path.exists(logo_path):
            logo_lbl = QLabel()
            logo_lbl.setAlignment(Qt.AlignCenter)
            pix = QPixmap(logo_path).scaled(190, 48, Qt.KeepAspectRatio, Qt.SmoothTransformation)
            logo_lbl.setPixmap(pix)
            top_layout.addWidget(logo_lbl)
        else:
            brand_title = QLabel("POP STAR FOOD")
            brand_title.setAlignment(Qt.AlignCenter)
            brand_title.setStyleSheet("font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: 2px;")
            top_layout.addWidget(brand_title)

        title = QLabel(f"POS SYSTEM (v{VERSION_STRING})")
        title.setAlignment(Qt.AlignCenter)
        title.setStyleSheet("font-size: 13px; font-weight: 700; color: #fee2e2; letter-spacing: 1.5px; margin-top: 2px;")
        top_layout.addWidget(title)

        layout.addWidget(top_header)

        # 2. Form Content Area (Clean White)
        content_widget = QWidget()
        content_widget.setStyleSheet("background-color: #ffffff;")
        content_layout = QVBoxLayout(content_widget)
        content_layout.setContentsMargins(36, 24, 36, 24)
        content_layout.setSpacing(14)

        # Field 1: Device Token
        f1_box = QVBoxLayout()
        f1_box.setSpacing(4)
        lbl1 = QLabel("รหัสเครื่องจุดขาย (Device Token)")
        lbl1.setStyleSheet("font-size: 12px; font-weight: 700; color: #374151;")
        f1_box.addWidget(lbl1)

        self.token_input = QLineEdit()
        self.token_input.setText(self.token)
        self.token_input.setStyleSheet("""
            QLineEdit {
                padding: 10px 14px;
                border: 1.5px solid #d1d5db;
                border-radius: 6px;
                font-size: 13px;
                color: #111827;
                background-color: #f9fafb;
            }
            QLineEdit:focus {
                border-color: #dc2626;
                background-color: #ffffff;
            }
        """)
        f1_box.addWidget(self.token_input)
        content_layout.addLayout(f1_box)

        # Field 2: Username
        f2_box = QVBoxLayout()
        f2_box.setSpacing(4)
        lbl2 = QLabel("ชื่อผู้ใช้งาน (Username)")
        lbl2.setStyleSheet("font-size: 12px; font-weight: 700; color: #374151;")
        f2_box.addWidget(lbl2)

        self.user_input = QLineEdit()
        self.user_input.setText(self.username)
        self.user_input.setStyleSheet("""
            QLineEdit {
                padding: 10px 14px;
                border: 1.5px solid #d1d5db;
                border-radius: 6px;
                font-size: 13px;
                color: #111827;
                background-color: #f9fafb;
            }
            QLineEdit:focus {
                border-color: #dc2626;
                background-color: #ffffff;
            }
        """)
        f2_box.addWidget(self.user_input)
        content_layout.addLayout(f2_box)

        # Field 3: Password / PIN
        f3_box = QVBoxLayout()
        f3_box.setSpacing(4)
        lbl3 = QLabel("รหัสผ่าน (Password)")
        lbl3.setStyleSheet("font-size: 12px; font-weight: 700; color: #374151;")
        f3_box.addWidget(lbl3)

        self.pin_input = QLineEdit()
        self.pin_input.setEchoMode(QLineEdit.Password)
        self.pin_input.setStyleSheet("""
            QLineEdit {
                padding: 10px 14px;
                border: 1.5px solid #d1d5db;
                border-radius: 6px;
                font-size: 13px;
                color: #111827;
                background-color: #f9fafb;
            }
            QLineEdit:focus {
                border-color: #dc2626;
                background-color: #ffffff;
            }
        """)
        self.pin_input.returnPressed.connect(self.do_verify_and_login)
        self.user_input.returnPressed.connect(self.pin_input.setFocus)
        self.token_input.returnPressed.connect(self.user_input.setFocus)
        f3_box.addWidget(self.pin_input)
        content_layout.addLayout(f3_box)

        # Status Label
        self.status_lbl = QLabel("")
        self.status_lbl.setAlignment(Qt.AlignCenter)
        self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626; min-height: 18px;")
        content_layout.addWidget(self.status_lbl)

        content_layout.addStretch()

        # Action Buttons
        btn_layout = QHBoxLayout()
        btn_layout.setSpacing(10)

        exit_btn = QPushButton("ปิด")
        exit_btn.setFixedSize(90, 44)
        exit_btn.setStyleSheet("""
            QPushButton {
                background-color: #f3f4f6;
                color: #4b5563;
                font-weight: 700;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                font-size: 13px;
            }
            QPushButton:hover {
                background-color: #e5e7eb;
                color: #111827;
            }
        """)
        exit_btn.clicked.connect(self.reject)

        self.login_btn = QPushButton("เข้าสู่ระบบ")
        self.login_btn.setFixedHeight(44)
        self.login_btn.setStyleSheet("""
            QPushButton {
                background-color: #dc2626;
                color: #ffffff;
                font-size: 15px;
                font-weight: 800;
                border: none;
                border-radius: 6px;
                letter-spacing: 0.5px;
            }
            QPushButton:hover {
                background-color: #b91c1c;
            }
            QPushButton:pressed {
                background-color: #991b1b;
            }
            QPushButton:disabled {
                background-color: #fca5a5;
                color: #ffffff;
            }
        """)
        self.login_btn.clicked.connect(self.do_verify_and_login)

        guide_btn = QPushButton("📖 คู่มือ")
        guide_btn.setFixedSize(80, 44)
        guide_btn.setStyleSheet("""
            QPushButton {
                background-color: #fef2f2;
                color: #dc2626;
                font-weight: 700;
                border: 1px solid #fecaca;
                border-radius: 6px;
                font-size: 13px;
            }
            QPushButton:hover {
                background-color: #fee2e2;
                color: #b91c1c;
            }
        """)
        guide_btn.clicked.connect(self.show_quick_guide)

        btn_layout.addWidget(guide_btn)
        btn_layout.addWidget(exit_btn)
        btn_layout.addWidget(self.login_btn)
        content_layout.addLayout(btn_layout)

        layout.addWidget(content_widget)

        # Auto Focus
        if not self.token:
            self.token_input.setFocus()
        elif not self.username:
            self.user_input.setFocus()
        else:
            self.pin_input.setFocus()

    def play_intro_sound(self):
        """เสียงแจ้งเตือนสั้นๆ นุ่มนวลเมื่อเข้าใช้งานสำเร็จ"""
        try:
            import winsound
            winsound.PlaySound('SystemAsterisk', winsound.SND_ALIAS | winsound.SND_ASYNC)
        except Exception:
            pass

    def do_verify_and_login(self):
        token = self.token_input.text().strip()
        username = self.user_input.text().strip()
        pin = self.pin_input.text().strip()

        if not token:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาระบุรหัสเครื่องจุดขาย (Token)")
            self.token_input.setFocus()
            return

        if not username:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาระบุชื่อผู้ใช้งาน")
            self.user_input.setFocus()
            return

        if not pin:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาระบุรหัสผ่าน")
            self.pin_input.setFocus()
            return

        self.login_btn.setEnabled(False)
        self.login_btn.setText("กำลังตรวจสอบ...")
        self.status_lbl.setText("กำลังเชื่อมต่อเซิร์ฟเวอร์...")
        self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626;")
        self.repaint()

        try:
            # 1. ตรวจสอบ Token
            ping_res = requests.get(
                f"{self.api_url}/api/pos/ping",
                headers={'Authorization': f"Bearer {token}", 'Accept': 'application/json'},
                timeout=6
            )
            if ping_res.status_code != 200:
                self.status_lbl.setText("Token ไม่ถูกต้อง หรือถูกเพิกถอน")
                self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626;")
                QMessageBox.warning(self, "ข้อผิดพลาด", "รหัสเครื่อง (Token) ไม่ถูกต้อง หรือถูกระงับการใช้งาน")
                self.login_btn.setEnabled(True)
                self.login_btn.setText("เข้าสู่ระบบ")
                return

            ping_data = ping_res.json()
            self.branch_id = ping_data.get('branch_id')
            self.branch_name = ping_data.get('branch_name', 'สำนักงานใหญ่')
            device_info = ping_data.get('device', {}) if isinstance(ping_data.get('device'), dict) else {}
            self.device_id = device_info.get('terminal_code') or str(device_info.get('id', 'POS-01'))
            self.device_name = device_info.get('name', 'POS Terminal')
            self.token = token

            # 2. ตรวจสอบ Cashier Credentials
            login_res = requests.post(
                f"{self.api_url}/api/pos/cashier/login",
                headers={'Authorization': f"Bearer {token}", 'Accept': 'application/json'},
                json={'code': username, 'pin': pin},
                timeout=6
            )
            login_res.encoding = 'utf-8'

            if login_res.status_code == 200:
                login_data = login_res.json()
                if login_data.get('success'):
                    self.selected_cashier = login_data.get('cashier', {})
                    if 'code' not in self.selected_cashier:
                        self.selected_cashier['code'] = username
                    self.entered_pin = pin
                    self.username = username

                    cashier_name = self.selected_cashier.get('name') or username
                    self.status_lbl.setText(f"เข้าสู่ระบบสำเร็จ: {cashier_name}")
                    self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #16a34a;")
                    self.repaint()

                    self.play_intro_sound()
                    QTimer.singleShot(350, self.accept)
                    return
                else:
                    msg = login_data.get('message', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง')
                    self.status_lbl.setText(msg)
                    self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626;")
                    QMessageBox.warning(self, "เข้าสู่ระบบไม่สำเร็จ", msg)
            elif login_res.status_code == 403:
                self.status_lbl.setText("บัญชีนี้ไม่มีสิทธิ์การขาย POS")
                self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626;")
                QMessageBox.warning(self, "ไม่มีสิทธิ์", "บัญชีผู้ใช้นี้ยังไม่ได้รับสิทธิ์การขายประจำเครื่อง POS")
            else:
                try:
                    err = login_res.json().get('message', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง')
                except Exception:
                    err = f"เกิดข้อผิดพลาดในการเชื่อมต่อ ({login_res.status_code})"
                self.status_lbl.setText(err)
                self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626;")
                QMessageBox.warning(self, "ข้อผิดพลาด", err)

        except Exception as e:
            self.status_lbl.setText("ไม่สามารถเชื่อมต่อเครือข่ายได้")
            self.status_lbl.setStyleSheet("font-size: 12px; font-weight: 700; color: #dc2626;")
            reply = QMessageBox.question(
                self, "Network Error",
                f"ไม่สามารถเชื่อมต่อไปยังเซิร์ฟเวอร์ได้\nต้องการเข้าสู่ระบบแบบออฟไลน์ด้วยบัญชี {username} หรือไม่?",
                QMessageBox.Yes | QMessageBox.No
            )
            if reply == QMessageBox.Yes:
                self.token = token
                self.username = username
                self.entered_pin = pin
                self.selected_cashier = {'code': username, 'name': f"พนักงาน ({username})"}
                self.play_intro_sound()
                self.accept()
                return

        finally:
            self.login_btn.setEnabled(True)
            self.login_btn.setText("เข้าสู่ระบบ")

    def show_quick_guide(self):
        from ui.manual_dialog import ManualDialog
        dialog = ManualDialog(self, initial_step=0)
        dialog.exec()

