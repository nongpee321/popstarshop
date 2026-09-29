import sys
import os

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.write('''import os
from PySide6.QtWidgets import (
    QDialog, QVBoxLayout, QHBoxLayout, QGridLayout, 
    QPushButton, QLabel, QFrame, QWidget, QLineEdit, QCheckBox, QMessageBox, QFormLayout
)
from PySide6.QtCore import Qt
from PySide6.QtGui import QFont, QIcon, QDoubleValidator

class PaymentDialog(QDialog):
    def __init__(self, parent=None, total_amount=0.0, total_items=0, total_qty=0.0):
        super().__init__(parent)
        self.setWindowTitle("รับชำระเงิน")
        self.setFixedSize(850, 600)
        self.setStyleSheet("background-color: white; color: #111827;")
        
        self.total_amount = total_amount
        self.total_items = total_items
        self.total_qty = total_qty
        
        self.selected_method = "เงินสด"
        self.received_amount = 0.0
        self.change_amount = 0.0
        self.cash_amount = 0.0
        self.transfer_amount = 0.0
        
        # Tax Invoice Data
        self.is_full_tax = False
        self.customer_name = ""
        self.customer_tax_id = ""
        self.customer_address = ""
        
        self.init_ui()
        self.update_displays()
        
    def init_ui(self):
        main_layout = QHBoxLayout(self)
        main_layout.setContentsMargins(0, 0, 0, 0)
        main_layout.setSpacing(0)
        
        # Left Panel (Gray)
        left_panel = QFrame()
        left_panel.setStyleSheet("background-color: #f3f4f6;")
        left_panel.setFixedWidth(350)
        left_layout = QVBoxLayout(left_panel)
        left_layout.setContentsMargins(20, 20, 20, 20)
        left_layout.setSpacing(20)
        
        # Payment Methods
        methods_layout = QGridLayout()
        self.method_btns = {}
        row, col = 0, 0
        for m in ["เงินสด", "QR", "ผสม (Split)"]:
            btn = QPushButton(m)
            btn.setCheckable(True)
            btn.setFixedHeight(60)
            btn.setStyleSheet("""
                QPushButton { background-color: white; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-weight: bold; color: #4b5563; }
                QPushButton:checked { border: 2px solid #10b981; color: #10b981; background-color: #ecfdf5; }
            """)
            btn.clicked.connect(lambda checked, method=m: self.set_method(method))
            self.method_btns[m] = btn
            methods_layout.addWidget(btn, row, col)
            col += 1
            if col > 1:
                col = 0
                row += 1
                
        self.method_btns["เงินสด"].setChecked(True)
        left_layout.addLayout(methods_layout)
        
        # Split Payment Inputs
        self.split_frame = QWidget()
        split_layout = QVBoxLayout(self.split_frame)
        split_layout.setContentsMargins(0,0,0,0)
        
        lbl_cash = QLabel("รับเงินสด:")
        self.inp_split_cash = QLineEdit()
        self.inp_split_cash.setPlaceholderText("0.00")
        self.inp_split_cash.setStyleSheet("padding: 10px; font-size: 16px; border-radius: 6px; border: 1px solid #cbd5e1;")
        
        lbl_transfer = QLabel("รับเงินโอน/QR:")
        self.inp_split_transfer = QLineEdit()
        self.inp_split_transfer.setPlaceholderText("0.00")
        self.inp_split_transfer.setStyleSheet("padding: 10px; font-size: 16px; border-radius: 6px; border: 1px solid #cbd5e1;")
        
        split_layout.addWidget(lbl_cash)
        split_layout.addWidget(self.inp_split_cash)
        split_layout.addWidget(lbl_transfer)
        split_layout.addWidget(self.inp_split_transfer)
        self.split_frame.hide()
        
        self.inp_split_cash.textChanged.connect(self.calculate_split)
        self.inp_split_transfer.textChanged.connect(self.calculate_split)
        
        left_layout.addWidget(self.split_frame)
        
        # Summary Card
        summary_card = QFrame()
        summary_card.setStyleSheet("background-color: white; border-radius: 12px; border: 1px solid #e5e7eb;")
        summary_layout = QVBoxLayout(summary_card)
        summary_layout.setSpacing(10)
        
        def add_summary_row(label, value):
            row = QHBoxLayout()
            lbl = QLabel(label)
            lbl.setStyleSheet("font-size: 14px; color: #374151; font-weight: bold; border: none;")
            val = QLabel(str(value))
            val.setStyleSheet("font-size: 14px; color: #111827; font-weight: bold; border: none;")
            row.addWidget(lbl)
            row.addStretch()
            row.addWidget(val)
            summary_layout.addLayout(row)
            
        add_summary_row("จำนวนรายการ", f"{self.total_items}")
        add_summary_row("จำนวนชิ้น", f"{self.total_qty:g}")
        
        total_lbl = QLabel(f"฿ {self.total_amount:,.2f}")
        total_lbl.setStyleSheet("font-size: 32px; font-weight: bold; color: #10b981; margin-top: 10px; border: none;")
        total_lbl.setAlignment(Qt.AlignCenter)
        summary_layout.addWidget(total_lbl)
        
        left_layout.addWidget(summary_card)
        left_layout.addStretch()
        main_layout.addWidget(left_panel)
        
        # Right Panel
        right_panel = QFrame()
        right_layout = QVBoxLayout(right_panel)
        right_layout.setContentsMargins(30, 30, 30, 30)
        right_layout.setSpacing(15)
        
        self.title_lbl = QLabel("รับเงินสด")
        self.title_lbl.setStyleSheet("font-size: 20px; font-weight: bold; color: #1f2937;")
        right_layout.addWidget(self.title_lbl)
        
        # Normal Input
        self.input_field = QLineEdit()
        self.input_field.setReadOnly(True)
        self.input_field.setAlignment(Qt.AlignRight)
        self.input_field.setStyleSheet("""
            QLineEdit { border: 2px solid #e5e7eb; border-radius: 12px; font-size: 36px; padding: 15px; color: #111827; }
        """)
        right_layout.addWidget(self.input_field)
        
        self.change_lbl = QLabel("เงินทอน: ฿ 0.00")
        self.change_lbl.setStyleSheet("font-size: 20px; font-weight: bold; color: #f59e0b;")
        self.change_lbl.setAlignment(Qt.AlignRight)
        right_layout.addWidget(self.change_lbl)
        
        # Keypad
        keypad_layout = QGridLayout()
        keypad_layout.setSpacing(10)
        
        keys = [
            ("7", 0, 0), ("8", 0, 1), ("9", 0, 2), ("1,000", 0, 3),
            ("4", 1, 0), ("5", 1, 1), ("6", 1, 2), ("500", 1, 3),
            ("1", 2, 0), ("2", 2, 1), ("3", 2, 2), ("100", 2, 3),
            ("0", 3, 0), ("00", 3, 1), (".", 3, 2), ("C", 3, 3)
        ]
        
        for key, r, c in keys:
            btn = QPushButton(key)
            btn.setFixedHeight(65)
            if key == "C":
                btn.setStyleSheet("background-color: #fee2e2; color: #ef4444; font-size: 24px; font-weight: bold; border-radius: 8px; border: none;")
                btn.clicked.connect(self.clear_input)
            elif key in ["1,000", "500", "100"]:
                btn.setStyleSheet("background-color: #f3f4f6; color: #374151; font-size: 20px; font-weight: bold; border-radius: 8px; border: none;")
                btn.clicked.connect(lambda checked, k=key: self.quick_cash(k))
            else:
                btn.setStyleSheet("background-color: white; color: #111827; font-size: 24px; font-weight: bold; border-radius: 8px; border: 1px solid #e5e7eb;")
                btn.clicked.connect(lambda checked, k=key: self.append_input(k))
            keypad_layout.addWidget(btn, r, c)
            
        self.keypad_widget = QWidget()
        self.keypad_widget.setLayout(keypad_layout)
        right_layout.addWidget(self.keypad_widget)
        
        # Tax Invoice Option
        self.tax_checkbox = QCheckBox("ขอใบกำกับภาษีเต็มรูปแบบ (Full Tax Invoice)")
        self.tax_checkbox.setStyleSheet("font-size: 16px; font-weight: bold; color: #374151;")
        self.tax_checkbox.stateChanged.connect(self.toggle_tax_form)
        right_layout.addWidget(self.tax_checkbox)
        
        self.tax_form = QWidget()
        form_l = QFormLayout(self.tax_form)
        self.inp_tax_name = QLineEdit()
        self.inp_tax_id = QLineEdit()
        self.inp_tax_address = QLineEdit()
        style = "padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
        self.inp_tax_name.setStyleSheet(style)
        self.inp_tax_id.setStyleSheet(style)
        self.inp_tax_address.setStyleSheet(style)
        form_l.addRow("ชื่อบริษัท/ลูกค้า:", self.inp_tax_name)
        form_l.addRow("เลขผู้เสียภาษี:", self.inp_tax_id)
        form_l.addRow("ที่อยู่:", self.inp_tax_address)
        self.tax_form.hide()
        right_layout.addWidget(self.tax_form)
        
        # Submit Button
        self.submit_btn = QPushButton("ยืนยันชำระเงิน (Enter)")
        self.submit_btn.setFixedHeight(60)
        self.submit_btn.setStyleSheet("background-color: #3b82f6; color: white; font-size: 22px; font-weight: bold; border-radius: 8px; border: none;")
        self.submit_btn.clicked.connect(self.accept_payment)
        right_layout.addWidget(self.submit_btn)
        
        main_layout.addWidget(right_panel)
        
        self.received_str = ""
        
    def set_method(self, method):
        for m, btn in self.method_btns.items():
            btn.setChecked(m == method)
        self.selected_method = method
        self.title_lbl.setText(f"ยอดรับชำระ: {method}")
        
        if method == "QR":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.hide()
            self.received_str = f"{self.total_amount:.2f}"
            self.update_displays()
        elif method == "ผสม (Split)":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.show()
            self.received_str = "0"
            self.change_amount = 0
            self.update_displays()
        else:
            self.keypad_widget.show()
            self.input_field.show()
            self.split_frame.hide()
            self.received_str = ""
            self.update_displays()
            
    def append_input(self, val):
        if val == "." and "." in self.received_str:
            return
        if self.received_str == "0" and val != ".":
            self.received_str = val
        else:
            self.received_str += val
        self.update_displays()
        
    def quick_cash(self, val):
        amount = float(val.replace(",", ""))
        current = float(self.received_str or 0)
        self.received_str = str(current + amount)
        self.update_displays()
        
    def clear_input(self):
        self.received_str = ""
        self.update_displays()
        
    def calculate_split(self):
        try:
            c = float(self.inp_split_cash.text() or 0)
            t = float(self.inp_split_transfer.text() or 0)
            total_rcv = c + t
            self.cash_amount = c
            self.transfer_amount = t
            self.received_amount = total_rcv
            if total_rcv >= self.total_amount:
                self.change_amount = total_rcv - self.total_amount
            else:
                self.change_amount = 0
            self.change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
        except:
            pass

    def update_displays(self):
        if self.selected_method != "ผสม (Split)":
            try:
                self.received_amount = float(self.received_str or 0)
            except ValueError:
                self.received_amount = 0.0
                
            self.input_field.setText(f"{self.received_amount:,.2f}")
            
            if self.received_amount >= self.total_amount:
                self.change_amount = self.received_amount - self.total_amount
                self.change_lbl.setStyleSheet("font-size: 20px; font-weight: bold; color: #10b981;")
            else:
                self.change_amount = 0.0
                self.change_lbl.setStyleSheet("font-size: 20px; font-weight: bold; color: #ef4444;")
                
            self.change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
            
            # Auto update split values for normal methods just in case
            if self.selected_method == "เงินสด":
                self.cash_amount = self.received_amount
                self.transfer_amount = 0
            elif self.selected_method == "QR":
                self.transfer_amount = self.total_amount
                self.cash_amount = 0
                
    def toggle_tax_form(self, state):
        self.is_full_tax = state == Qt.Checked
        if self.is_full_tax:
            self.tax_form.show()
            self.setFixedSize(850, 680)
        else:
            self.tax_form.hide()
            self.setFixedSize(850, 600)

    def accept_payment(self):
        if self.selected_method == "ผสม (Split)":
            if self.received_amount < self.total_amount:
                QMessageBox.warning(self, "แจ้งเตือน", "ยอดชำระยังไม่ครบตามบิล!")
                return
        else:
            if self.received_amount < self.total_amount:
                QMessageBox.warning(self, "แจ้งเตือน", "รับเงินมาไม่ครบ!")
                return
                
        if self.is_full_tax:
            self.customer_name = self.inp_tax_name.text().strip()
            self.customer_tax_id = self.inp_tax_id.text().strip()
            self.customer_address = self.inp_tax_address.text().strip()
            if not self.customer_name or not self.customer_tax_id:
                QMessageBox.warning(self, "แจ้งเตือน", "กรุณากรอกชื่อและเลขผู้เสียภาษีให้ครบถ้วน")
                return
                
        # For split, adjust cash_amount if there's change (change is returned from cash)
        if self.selected_method == "ผสม (Split)" and self.change_amount > 0:
            if self.cash_amount >= self.change_amount:
                self.cash_amount -= self.change_amount
            else:
                # Edge case: change is more than cash given? Should not happen logically if they transfer correct amount, but just in case
                self.cash_amount = 0
                
        self.accept()

''')

print("Payment Dialog written.")
