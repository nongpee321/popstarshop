import sys
import os
from PySide6.QtWidgets import (QMainWindow, QWidget, QVBoxLayout, QHBoxLayout, 
                               QLabel, QTableWidget, QTableWidgetItem, QHeaderView)
from PySide6.QtCore import Qt
from PySide6.QtGui import QPixmap, QFont, QColor, QImage
import qrcode

def crc16(data: str) -> str:
    crc = 0xFFFF
    for char in data:
        crc ^= (ord(char) << 8)
        for _ in range(8):
            if crc & 0x8000:
                crc = (crc << 1) ^ 0x1021
            else:
                crc <<= 1
            crc &= 0xFFFF
    return f"{crc:04X}"

def generate_promptpay(promptpay_id: str, amount: float = 0) -> str:
    promptpay_id = ''.join(filter(str.isdigit, promptpay_id))
    if len(promptpay_id) >= 15:
        target = f"0315{promptpay_id}"
    elif len(promptpay_id) == 13:
        target = f"0213{promptpay_id}"
    elif len(promptpay_id) == 10:
        target = f"01130066{promptpay_id[1:]}"
    else:
        target = f"01130066{promptpay_id[1:]}"
    merchant_info = f"0016A000000677010111{target}"
    payload = [
        "000201",
        "010212", # 12 for Dynamic (amount included)
        f"29{len(merchant_info):02d}{merchant_info}",
        "5802TH",
        "5303764",
    ]
    if amount > 0:
        amt_str = f"{amount:.2f}"
        payload.append(f"54{len(amt_str):02d}{amt_str}")
    payload.append("6304")
    data_to_crc = "".join(payload)
    return data_to_crc + crc16(data_to_crc)

class CustomerDisplayWindow(QMainWindow):
    def __init__(self, parent=None, config=None):
        super().__init__(parent)
        self.config = config or {}
        self.setWindowTitle("Customer Display")
        self.setMinimumSize(1024, 768)
        self.setStyleSheet('''
            QMainWindow { background-color: #0f172a; } /* Dark Mode */
            QLabel { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
            
            QTableWidget { 
                font-size: 24px; 
                background-color: #1e293b; 
                color: white;
                border-radius: 12px; 
                border: 1px solid #334155; 
                selection-background-color: #1e293b;
                gridline-color: #334155;
            }
            QTableWidget::item {
                padding: 15px;
                border-bottom: 1px solid #334155;
            }
            
            QHeaderView::section {
                background-color: #3b82f6;
                color: white;
                font-size: 22px;
                font-weight: bold;
                border: none;
                padding: 15px;
            }
            
            #totalLabel {
                font-size: 80px;
                font-weight: 900;
                color: #22c55e; /* Green */
                background-color: #1e293b;
                border-radius: 16px;
                padding: 20px 40px;
                border: 2px solid #22c55e;
                margin-top: 10px;
                margin-bottom: 20px;
            }
            
            #statusLabel {
                font-size: 40px;
                font-weight: bold;
                color: #cbd5e1;
                margin-top: 10px;
            }
            
            #thankLabel {
                font-size: 48px;
                font-weight: 900;
                color: #3b82f6;
                margin-top: 40px;
            }
        ''')

        central = QWidget()
        self.setCentralWidget(central)
        main_layout = QHBoxLayout(central)
        main_layout.setContentsMargins(40, 40, 40, 40)
        main_layout.setSpacing(50)

        # Left Cart
        left_layout = QVBoxLayout()
        title_label = QLabel("รายการสินค้า (Your Order)")
        title_label.setFont(QFont("Segoe UI", 32, QFont.Bold))
        title_label.setStyleSheet("color: white; margin-bottom: 20px;")
        left_layout.addWidget(title_label)

        self.table = QTableWidget(0, 4)
        self.table.setHorizontalHeaderLabels(["สินค้า", "จำนวน", "ราคา", "รวม"])
        h = self.table.horizontalHeader()
        h.setSectionResizeMode(0, QHeaderView.Stretch)
        h.setSectionResizeMode(1, QHeaderView.ResizeToContents)
        h.setSectionResizeMode(2, QHeaderView.ResizeToContents)
        h.setSectionResizeMode(3, QHeaderView.ResizeToContents)
        self.table.setEditTriggers(QTableWidget.NoEditTriggers)
        self.table.setFocusPolicy(Qt.NoFocus)
        self.table.verticalHeader().hide()
        self.table.setShowGrid(False)
        self.table.setAlternatingRowColors(True)
        self.table.setStyleSheet(self.table.styleSheet() + "QTableWidget { alternate-background-color: #0f172a; }")
        left_layout.addWidget(self.table)

        # Right Pay
        right_layout = QVBoxLayout()
        right_layout.setAlignment(Qt.AlignTop | Qt.AlignHCenter)
        
        self.status = QLabel("ยอดรวมทั้งสิ้น")
        self.status.setObjectName("statusLabel")
        self.status.setAlignment(Qt.AlignCenter)
        right_layout.addWidget(self.status)

        self.total = QLabel("฿ 0.00")
        self.total.setObjectName("totalLabel")
        self.total.setAlignment(Qt.AlignCenter)
        right_layout.addWidget(self.total)

        self.qr = QLabel()
        self.qr.setAlignment(Qt.AlignCenter)
        self.qr.setStyleSheet("background-color: white; border-radius: 16px; padding: 20px; border: 4px solid #3b82f6;")
        self.qr.hide()
        right_layout.addWidget(self.qr)
        
        self.thank = QLabel("ขอบคุณที่ใช้บริการ\\nThank You")
        self.thank.setObjectName("thankLabel")
        self.thank.setAlignment(Qt.AlignCenter)
        self.thank.hide()
        right_layout.addWidget(self.thank)

        main_layout.addLayout(left_layout, 6)
        main_layout.addLayout(right_layout, 4)
        self.current_total = 0.0

    def update_cart(self, table_widget, total_amount):
        self.current_total = total_amount
        self.qr.hide()
        self.thank.hide()
        self.status.setText("ยอดรวมทั้งสิ้น")
        self.status.setStyleSheet("color: #cbd5e1;")
        self.total.setText(f"฿ {total_amount:,.2f}")
        self.total.setStyleSheet("color: #22c55e; border-color: #22c55e;")
        
        self.table.setRowCount(0)
        rows = table_widget.rowCount()
        for i in range(rows):
            self.table.insertRow(i)
            self.table.setRowHeight(i, 70)
            
            name = table_widget.item(i, 0).text() if table_widget.item(i, 0) else ""
            qty = table_widget.item(i, 1).text() if table_widget.item(i, 1) else ""
            price = table_widget.item(i, 2).text() if table_widget.item(i, 2) else ""
            total = table_widget.item(i, 3).text() if table_widget.item(i, 3) else ""
            
            ni = QTableWidgetItem(name)
            font = QFont()
            font.setBold(True)
            ni.setFont(font)
            self.table.setItem(i, 0, ni)
            
            qi = QTableWidgetItem(qty)
            qi.setTextAlignment(Qt.AlignCenter)
            self.table.setItem(i, 1, qi)
            
            pi = QTableWidgetItem(price)
            pi.setTextAlignment(Qt.AlignRight | Qt.AlignVCenter)
            self.table.setItem(i, 2, pi)
            
            ti = QTableWidgetItem(total)
            ti.setTextAlignment(Qt.AlignRight | Qt.AlignVCenter)
            ti.setFont(font)
            ti.setForeground(QColor("#38bdf8"))
            self.table.setItem(i, 3, ti)

    def generate_qr_image(self, amount):
        promptpay_id = self.config.get('promptpay_id', '0999999999') # Config fallback
        payload = generate_promptpay(promptpay_id, amount)
        qr_img = qrcode.make(payload)
        
        # Convert PIL image to QPixmap
        qr_img = qr_img.convert("RGBA")
        data = qr_img.tobytes("raw", "RGBA")
        qim = QImage(data, qr_img.size[0], qr_img.size[1], QImage.Format_RGBA8888)
        pixmap = QPixmap.fromImage(qim)
        return pixmap.scaled(450, 450, Qt.KeepAspectRatio, Qt.SmoothTransformation)

    def show_payment(self, method="QR"):
        self.status.setText("กรุณาสแกนจ่ายเงิน")
        self.status.setStyleSheet("color: #38bdf8;")
        self.total.setStyleSheet("color: #38bdf8; border-color: #38bdf8;")
        if method == "QR":
            try:
                pixmap = self.generate_qr_image(self.current_total)
                self.qr.setPixmap(pixmap)
            except Exception as e:
                print(e)
            self.qr.show()
        else:
            self.qr.hide()

    def show_success(self, change=0):
        self.table.setRowCount(0)
        self.qr.hide()
        self.status.setText("ชำระเงินสำเร็จ")
        self.status.setStyleSheet("color: #22c55e;")
        if change > 0:
            self.total.setText(f"เงินทอน ฿ {change:,.2f}")
            self.total.setStyleSheet("color: #facc15; border-color: #facc15;")
        else:
            self.total.setText("เรียบร้อย")
            self.total.setStyleSheet("color: #22c55e; border-color: #22c55e;")
        self.thank.show()
