import sys
import re

# 1. Update customer_display.py to be beautiful
cfd_code = '''import sys
import os
from PySide6.QtWidgets import (QMainWindow, QWidget, QVBoxLayout, QHBoxLayout, 
                               QLabel, QTableWidget, QTableWidgetItem, QHeaderView)
from PySide6.QtCore import Qt, QSize
from PySide6.QtGui import QPixmap, QFont, QColor

class CustomerDisplayWindow(QMainWindow):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setWindowTitle("Customer Display")
        self.setMinimumSize(1024, 768)
        self.setStyleSheet("""
            QMainWindow { background-color: #f1f5f9; }
            QLabel { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
            
            QTableWidget { 
                font-size: 22px; 
                background-color: white; 
                border-radius: 12px; 
                border: 2px solid #e2e8f0; 
                selection-background-color: #f1f5f9;
                selection-color: #0f172a;
                gridline-color: #f1f5f9;
            }
            QTableWidget::item {
                padding: 12px;
                border-bottom: 1px solid #f1f5f9;
            }
            
            QHeaderView::section {
                background-color: #334155;
                color: white;
                font-size: 20px;
                font-weight: bold;
                border: none;
                padding: 15px;
            }
            
            #totalLabel {
                font-size: 72px;
                font-weight: 900;
                color: #e11d48;
                background-color: white;
                border-radius: 16px;
                padding: 10px 30px;
                border: 2px solid #fecdd3;
                margin-top: 20px;
                margin-bottom: 30px;
            }
            
            #statusLabel {
                font-size: 36px;
                font-weight: bold;
                color: #475569;
                margin-top: 20px;
            }
            
            #thankLabel {
                font-size: 42px;
                font-weight: 900;
                color: #059669;
                margin-top: 50px;
            }
        """)

        central = QWidget()
        self.setCentralWidget(central)
        main_layout = QHBoxLayout(central)
        main_layout.setContentsMargins(30, 30, 30, 30)
        main_layout.setSpacing(40)

        # Left Cart
        left_layout = QVBoxLayout()
        title_label = QLabel("รายการสินค้า (Your Order)")
        title_label.setFont(QFont("Segoe UI", 28, QFont.Bold))
        title_label.setStyleSheet("color: #0f172a; margin-bottom: 15px;")
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
        self.table.setStyleSheet(self.table.styleSheet() + "QTableWidget { alternate-background-color: #f8fafc; }")
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
        
        # Load QR
        base_path = os.path.dirname(os.path.dirname(__file__))
        if getattr(sys, 'frozen', False):
            base_path = os.path.dirname(sys.executable)
            
        qr_path = os.path.join(base_path, 'assets', 'qr_code.jpg')
        if os.path.exists(qr_path):
            pixmap = QPixmap(qr_path)
            self.qr.setPixmap(pixmap.scaled(450, 500, Qt.KeepAspectRatio, Qt.SmoothTransformation))
            self.qr.setStyleSheet("background-color: white; border-radius: 16px; padding: 20px; border: 1px solid #cbd5e1;")
        else:
            self.qr.setText("[ไม่พบไฟล์รูป QR Code]")
            self.qr.setFont(QFont("Segoe UI", 20))
            
        self.qr.hide()
        right_layout.addWidget(self.qr)
        
        self.thank = QLabel("ขอบคุณที่ใช้บริการ\\nThank You")
        self.thank.setObjectName("thankLabel")
        self.thank.setAlignment(Qt.AlignCenter)
        self.thank.hide()
        right_layout.addWidget(self.thank)

        main_layout.addLayout(left_layout, 6)
        main_layout.addLayout(right_layout, 4)

    def update_cart(self, table_widget, total_amount):
        self.qr.hide()
        self.thank.hide()
        self.status.setText("ยอดรวมทั้งสิ้น")
        self.status.setStyleSheet("color: #475569;")
        self.total.setText(f"฿ {total_amount:,.2f}")
        self.total.setStyleSheet("color: #e11d48; border-color: #fecdd3;")
        
        self.table.setRowCount(0)
        rows = table_widget.rowCount()
        for i in range(rows):
            self.table.insertRow(i)
            self.table.setRowHeight(i, 60)
            
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
            ti.setForeground(QColor("#0f172a"))
            self.table.setItem(i, 3, ti)

    def show_payment(self, method="QR"):
        self.status.setText("กรุณาสแกนจ่ายเงิน")
        self.status.setStyleSheet("color: #2563eb;")
        self.total.setStyleSheet("color: #2563eb; border-color: #bfdbfe;")
        if method == "QR":
            self.qr.show()
        else:
            self.qr.hide()

    def show_success(self, change=0):
        self.table.setRowCount(0)
        self.qr.hide()
        self.status.setText("ชำระเงินสำเร็จ")
        self.status.setStyleSheet("color: #059669;")
        if change > 0:
            self.total.setText(f"เงินทอน ฿ {change:,.2f}")
            self.total.setStyleSheet("color: #0284c7; border-color: #bae6fd;")
        else:
            self.total.setText("เรียบร้อย")
            self.total.setStyleSheet("color: #059669; border-color: #a7f3d0;")
        self.thank.show()
'''
with open(r'D:\pop-erp-food\python-pos\ui\customer_display.py', 'w', encoding='utf-8') as f:
    f.write(cfd_code)

# 2. Update main_window.py to actually call update_cart
with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

bad_update_total = '''            self.total_val.setText(f"฿{total:,.2f}")'''
good_update_total = '''            self.total_val.setText(f"฿{total:,.2f}")
            if hasattr(self, 'customer_display') and self.customer_display and not self.customer_display.isHidden():
                self.customer_display.update_cart(self.cart_table, total)'''

if bad_update_total in content:
    content = content.replace(bad_update_total, good_update_total)

# Replace version with 1.10.53
content = content.replace('v1.10.52', 'v1.10.53')

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)

print("Done!")
