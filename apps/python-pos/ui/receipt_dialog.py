# -*- coding: utf-8 -*-
from PySide6.QtWidgets import (QGraphicsDropShadowEffect, QDialog, QHBoxLayout, QVBoxLayout, QFrame,

                               QFormLayout, QComboBox, QLineEdit, QTextEdit,

                               QPushButton, QLabel, QScrollArea, QWidget, QMessageBox)

from PySide6.QtCore import Qt, QLocale, QUrl, QTimer, QRectF
from PySide6.QtGui import QColor, QImage, QPainter

from PySide6.QtGui import QFont

from PySide6.QtPrintSupport import QPrinterInfo

import os

import json
import html
import sys
from decimal import Decimal
from datetime import datetime


def _receipt_amount(value):
    return Decimal(str(value or 0))


def _payment_method_label(method):
    labels = {
        'cash': 'เงินสด',
        'transfer': 'โอนเงิน/QR',
        'mixed': 'เงินสด + โอนเงิน/QR',
        'split': 'เงินสด + โอนเงิน/QR',
        'เงินสด': 'เงินสด',
        'ผสม (Split)': 'เงินสด + โอนเงิน/QR',
    }
    return labels.get(method, str(method or 'ไม่ระบุ'))


def _receipt_date_label(value):
    date_text = str(value or '-')
    try:
        date = datetime.fromisoformat(date_text)
    except ValueError:
        return date_text
    thai_year = (date.year + 543) % 100
    return date.strftime('%d/%m/') + f'{thai_year:02d} ' + date.strftime('%H:%M')


def _logo_path():
    app_directory = getattr(sys, '_MEIPASS', os.path.dirname(sys.executable)) if getattr(sys, 'frozen', False) else os.path.dirname(os.path.dirname(__file__))
    return os.path.join(app_directory, 'assets', 'logo.png')


def _escpos_logo():
    path = _logo_path()
    if not os.path.isfile(path):
        return b''

    from PIL import Image

    image = Image.open(path).convert('L')
    content = image.point(lambda pixel: 255 if pixel < 245 else 0).getbbox()
    if content:
        image = image.crop(content)
    target_width = 128
    target_height = max(1, round(image.height * target_width / image.width))
    image = image.resize((target_width, target_height), Image.Resampling.LANCZOS)
    pixels = image.point(lambda pixel: 0 if pixel < 180 else 1, mode='1')
    row_bytes = (target_width + 7) // 8
    raster = bytearray()
    for y in range(target_height):
        for byte_index in range(row_bytes):
            value = 0
            for bit in range(8):
                x = byte_index * 8 + bit
                if x < target_width and pixels.getpixel((x, y)) == 0:
                    value |= 1 << (7 - bit)
            raster.append(value)

    width_low, width_high = row_bytes & 0xFF, row_bytes >> 8
    height_low, height_high = target_height & 0xFF, target_height >> 8
    return b'\x1dv0\x00' + bytes((width_low, width_high, height_low, height_high)) + bytes(raster) + b'\n'


def _receipt_raster_commands(document):
    source_width = document.textWidth()
    if source_width <= 0:
        raise ValueError("ไม่สามารถอ่านขนาดใบเสร็จสำหรับการพิมพ์ได้")

    paper_width = 576
    scale = paper_width / source_width
    source_height = document.documentLayout().documentSize().height()
    image_height = max(1, int(source_height * scale) + 8)
    image = QImage(paper_width, image_height, QImage.Format.Format_RGB32)
    image.fill(0xFFFFFFFF)

    painter = QPainter(image)
    painter.setRenderHint(QPainter.RenderHint.TextAntialiasing)
    painter.scale(scale, scale)
    document.drawContents(painter, QRectF(0, 0, source_width, source_height))
    painter.end()

    row_bytes = (paper_width + 7) // 8
    commands = bytearray(b'\x1b@\x1ba\x01')
    for start_y in range(0, image_height, 128):
        band_height = min(128, image_height - start_y)
        commands.extend((
            0x1D, 0x76, 0x30, 0x00,
            row_bytes & 0xFF, row_bytes >> 8,
            band_height & 0xFF, band_height >> 8,
        ))
        for y in range(start_y, start_y + band_height):
            for byte_index in range(row_bytes):
                value = 0
                for bit in range(8):
                    x = byte_index * 8 + bit
                    if x < paper_width and image.pixelColor(x, y).value() < 192:
                        value |= 1 << (7 - bit)
                commands.append(value)

    commands.extend(b'\n\n\n\x1dVA\x03')
    return bytes(commands)


def _send_raw_to_printer(printer_name, commands):
    try:
        import win32print
    except ImportError:
        raise Exception("ไม่พบโมดูล win32print กรุณาติดตั้ง pypiwin32")

    try:
        printer = win32print.OpenPrinter(printer_name)
        try:
            win32print.StartDocPrinter(printer, 1, ("PopCentralPOS Receipt", None, "RAW"))
            win32print.StartPagePrinter(printer)
            win32print.WritePrinter(printer, commands)
            win32print.EndPagePrinter(printer)
            win32print.EndDocPrinter(printer)
        finally:
            win32print.ClosePrinter(printer)
    except Exception as error:
        raise Exception(f"ไม่สามารถส่งข้อมูลไปที่เครื่องพิมพ์ '{printer_name}' ได้: {str(error)}")


def print_escpos_receipt(printer_name, receipt_data, config):
    if not printer_name:
        raise Exception("กรุณาเลือกเครื่องพิมพ์ก่อน")

    document = config.get('_receipt_document')
    if document is not None:
        _send_raw_to_printer(printer_name, _receipt_raster_commands(document))
        return

    def enc(text):

        try:

            return text.encode('tis-620', errors='replace')

        except LookupError:

            return text.encode('cp874', errors='replace')

    

    header_text = config.get('receipt_header', '')

    if not header_text:

        header_text = "บริษัท ป๊อปสตาร์ฟู้ดส์ เทรดดิ้ง จำกัด\n480 ถนนวารินชำราบ-ศรีสะเกษ ม.1 ต.แสนสุข\nอ.วารินชำราบ จ.อุบลราชธานี 34190\nโทร. 061-935-4497 | Line: @popstarshop"

        

    footer_text = config.get('receipt_footer', '')

    if not footer_text:

        footer_text = "ขอบคุณที่ใช้บริการ\nโอกาสหน้าเชิญใหม่ครับ/ค่ะ"



    branch = receipt_data.get('branch', 'สำนักงานใหญ่')

    cashier = receipt_data.get('cashier', 'พนักงาน')

    receipt_no = receipt_data.get('doc_number', '-')

    date_str = _receipt_date_label(receipt_data.get('date', '-'))

    total_amount = receipt_data.get('total', 0.0)

    

    pm = receipt_data.get('payment_method', 'cash')
    method_str = _payment_method_label(pm)

    

    is_full_tax = receipt_data.get('is_full_tax', False)

    cust_name = receipt_data.get('customer_name', '') or ''

    cust_tax = receipt_data.get('customer_tax_id', '') or ''

    cust_addr = receipt_data.get('customer_address', '') or ''

    

    ESC = b'\x1b'

    GS  = b'\x1d'

    INIT          = ESC + b'@'
    ALIGN_LEFT    = ESC + b'a\x00'
    ALIGN_CENTER  = ESC + b'a\x01'
    ALIGN_RIGHT   = ESC + b'a\x02'
    FONT_SMALL    = ESC + b'M\x01'
    FONT_NORMAL   = ESC + b'M\x00'

    BOLD_ON       = ESC + b'E\x01'

    BOLD_OFF      = ESC + b'E\x00'

    DOUBLE_ON     = GS  + b'!\x11'

    DOUBLE_OFF    = GS  + b'!\x00'

    LINE_FEED     = b'\n'

    CUT_PAPER     = GS  + b'V\x41\x03'
    OPEN_DRAWER_1 = ESC + b'p\x00\x19\xfa'
    OPEN_DRAWER_2 = ESC + b'p\x01\x19\xfa'

    

    cmds = bytearray()

    cmds += INIT
    cmds += FONT_SMALL
    
    cmds += ALIGN_CENTER

    cmds += _escpos_logo()
    
    cmds += BOLD_ON
    for line in header_text.split('\n'):

        cmds += enc(line) + LINE_FEED
    cmds += BOLD_OFF
    cmds += enc("ใบกำกับภาษีอย่างย่อ") + LINE_FEED

        

    

    cmds += ALIGN_LEFT
    if is_full_tax:
        cmds += enc(f"นามลูกค้า: {cust_name}") + LINE_FEED
        cmds += enc(f"เลขผู้เสียภาษี: {cust_tax}") + LINE_FEED
        cmds += enc(f"ที่อยู่: {cust_addr}") + LINE_FEED
        cmds += enc("-" * 48) + LINE_FEED
        
    cmds += enc(f"สาขา: {branch}    แคชเชียร์: {cashier}") + LINE_FEED
    cmds += enc(f"เลขที่: {receipt_no}") + LINE_FEED
    cmds += ALIGN_RIGHT
    cmds += enc(f"วันที่: {date_str}") + LINE_FEED
    cmds += ALIGN_LEFT
    cmds += enc("-" * 48) + LINE_FEED
    for item in receipt_data.get('items', []):
        name  = item.get('name', '')
        qty   = item.get('qty', 1)
        price = item.get('price', 0.0)
        total = item.get('total', 0.0)
        name_text = str(name)[:32]
        total_text = f"{total:,.2f}"
        padding = max(1, 48 - len(name_text) - len(total_text))
        cmds += enc(name_text + " " * padding + total_text) + LINE_FEED
        cmds += enc(f"  {qty} x {price:,.2f}") + LINE_FEED
    
    cmds += enc('-' * 48) + LINE_FEED
    
    vat_rate = _receipt_amount(receipt_data.get('vat_rate', 7))
    net_amount = _receipt_amount(total_amount)
    vat_amount = net_amount * vat_rate / (Decimal('100') + vat_rate) if vat_rate else Decimal('0')
    before_vat = net_amount - vat_amount
    cmds += enc(f"มูลค่าสินค้า (ก่อน VAT) {before_vat:,.2f}") + LINE_FEED
    cmds += enc(f"ภาษีมูลค่าเพิ่ม {vat_rate}% {vat_amount:,.2f}") + LINE_FEED
    cmds += ALIGN_CENTER
    cmds += BOLD_ON
    cmds += FONT_NORMAL
    cmds += enc(f'ยอดสุทธิ {net_amount:,.2f} บาท') + LINE_FEED
    cmds += FONT_SMALL
    cmds += BOLD_OFF
    cmds += ALIGN_CENTER
    cmds += enc('(รวมภาษีมูลค่าเพิ่มแล้ว)') + LINE_FEED
    cmds += enc(f'ชำระโดย: {method_str}') + LINE_FEED
    cmds += enc('-' * 48) + LINE_FEED

    

    cmds += BOLD_ON

    for line in footer_text.split('\n'):

        cmds += enc(line) + LINE_FEED

    cmds += BOLD_OFF

    cmds += LINE_FEED * 3

    cmds += CUT_PAPER

    

    try:

        import win32print

    except ImportError:

        raise Exception("ไม่พบโมดูล win32print กรุณาติดตั้ง pypiwin32")



    if not printer_name:

        raise Exception("กรุณาเลือกเครื่องพิมพ์ก่อน")



    try:

        hPrinter = win32print.OpenPrinter(printer_name)

        try:

            win32print.StartDocPrinter(hPrinter, 1, ("PopCentralPOS Receipt", None, "RAW"))

            win32print.StartPagePrinter(hPrinter)

            win32print.WritePrinter(hPrinter, bytes(cmds))

            win32print.EndPagePrinter(hPrinter)

            win32print.EndDocPrinter(hPrinter)

        finally:

            win32print.ClosePrinter(hPrinter)

    except Exception as e:

        raise Exception(f"ไม่สามารถส่งข้อมูลไปที่เครื่องพิมพ์ '{printer_name}' ได้: {str(e)}")







def open_cash_drawer(printer_name):

    try:

        import win32print

    except ImportError:

        raise Exception("Cannot import win32print")

        

    if not printer_name:

        raise Exception("No printer configured")

        

    ESC = b''

    INIT = ESC + b'@'

    OPEN_DRAWER_1 = ESC + b'p\x00\x19\xfa'

    OPEN_DRAWER_2 = ESC + b'p\x01\x19\xfa'

    

    cmds = bytearray()

    cmds += INIT
    cmds += OPEN_DRAWER_1
    cmds += OPEN_DRAWER_2

    

    try:

        hPrinter = win32print.OpenPrinter(printer_name)

        try:

            win32print.StartDocPrinter(hPrinter, 1, ("OpenDrawer", None, "RAW"))

            win32print.StartPagePrinter(hPrinter)

            win32print.WritePrinter(hPrinter, bytes(cmds))

            win32print.EndPagePrinter(hPrinter)

            win32print.EndDocPrinter(hPrinter)

        finally:

            win32print.ClosePrinter(hPrinter)

    except Exception as e:

        raise Exception(f"Failed to open drawer: {str(e)}")



class ReceiptSettingsDialog(QDialog):

    def __init__(self, parent=None, config=None, receipt_data=None):

        super().__init__(parent)

        self.setWindowTitle("ตั้งค่าใบเสร็จ")

        self.setFixedSize(950, 620)

        self.setStyleSheet("background-color: #f3f4f6; color: #111827; font-family: Tahoma, sans-serif;")

        self.config = config or {}

        self.receipt_data = receipt_data or {}

        

        self.setLocale(QLocale(QLocale.Language.English, QLocale.Country.UnitedStates))

        main_layout = QHBoxLayout(self)

        

        settings_frame = QFrame()
        settings_frame.setStyleSheet('''
            QFrame { background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; }
            QLabel { font-size: 15px; font-weight: bold; color: #1e293b; border: none; }
            QComboBox { 
                border: 2px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; 
                font-size: 14px; background-color: #f8fafc; min-width: 200px;
            }
            QComboBox:focus { border-color: #3b82f6; background-color: #ffffff; }
            QComboBox::drop-down { border: none; width: 30px; }
            QTextEdit { 
                border: 2px solid #cbd5e1; border-radius: 6px; padding: 10px; 
                font-size: 14px; background-color: #f8fafc;
            }
            QTextEdit:focus { border-color: #3b82f6; background-color: #ffffff; }
        ''')
        
        settings_layout = QVBoxLayout(settings_frame)
        settings_layout.setContentsMargins(24, 24, 24, 24)
        settings_layout.setSpacing(16)
        
        title_lbl = QLabel("⚙️ ตั้งค่าเครื่องพิมพ์และรูปแบบใบเสร็จ")
        title_lbl.setStyleSheet("font-size: 18px; color: #0f172a; margin-bottom: 8px; border: none;")
        settings_layout.addWidget(title_lbl)
        
        # Printer selection
        printer_layout = QVBoxLayout()
        printer_layout.setSpacing(6)
        lbl_printer = QLabel("🖨️ เลือกเครื่องพิมพ์ (ขนาด 80mm):")
        self.printer_combo = QComboBox()
        self.printer_combo.addItem("--- กรุณาเลือกเครื่องพิมพ์ ---")
        for printer in QPrinterInfo.availablePrinters():
            self.printer_combo.addItem(printer.printerName())
            
        current_printer = self.config.get('receipt_printer', '')
        if current_printer:
            idx = self.printer_combo.findText(current_printer)
            if idx >= 0:
                self.printer_combo.setCurrentIndex(idx)
        
        printer_layout.addWidget(lbl_printer)
        printer_layout.addWidget(self.printer_combo)
        settings_layout.addLayout(printer_layout)
        
        # Header text
        header_layout = QVBoxLayout()
        header_layout.setSpacing(6)
        lbl_header = QLabel("📝 ข้อความหัวใบเสร็จ (ชื่อร้าน/ที่อยู่/ติดต่อ):")
        self.header_edit = QTextEdit()
        self.header_edit.setPlaceholderText("ชื่อร้าน\nที่อยู่\nเบอร์โทร")
        default_header = "บริษัท ป๊อปสตาร์ฟู้ดส์ เทรดดิ้ง จำกัด\n480 หมู่บ้านคำเจริญ ม.1 ต.แสนสุข\nอ.วารินชำราบ จ.อุบลราชธานี 34190\nโทร. 061-935-4497 | Line: @popstarshop"
        self.header_edit.setPlainText(self.config.get('receipt_header', default_header))
        self.header_edit.setMinimumHeight(100)
        
        header_layout.addWidget(lbl_header)
        header_layout.addWidget(self.header_edit)
        settings_layout.addLayout(header_layout)
        
        # Footer text
        footer_layout = QVBoxLayout()
        footer_layout.setSpacing(6)
        lbl_footer = QLabel("📝 ข้อความท้ายใบเสร็จ (ขอบคุณลูกค้า):")
        self.footer_edit = QTextEdit()
        self.footer_edit.setPlaceholderText("ข้อความขอบคุณ")
        default_footer = "ขอบคุณที่ใช้บริการ\nโอกาสหน้าเชิญใหม่ครับ/ค่ะ"
        self.footer_edit.setPlainText(self.config.get('receipt_footer', default_footer))
        self.footer_edit.setMinimumHeight(80)
        
        footer_layout.addWidget(lbl_footer)
        footer_layout.addWidget(self.footer_edit)
        settings_layout.addLayout(footer_layout)
        
        settings_layout.addStretch()
        
        # Save Button
        save_btn = QPushButton("💾 บันทึกและทดสอบพิมพ์")
        save_btn.setStyleSheet('''
            QPushButton {
                background-color: #3b82f6; color: white; padding: 14px; 
                font-size: 16px; border-radius: 8px; font-weight: bold; border: none;
            }
            QPushButton:hover { background-color: #2563eb; }
            QPushButton:pressed { background-color: #1d4ed8; }
        ''')
        save_btn.setCursor(Qt.CursorShape.PointingHandCursor)
        save_btn.clicked.connect(self.save_and_test)
        settings_layout.addWidget(save_btn)
        
        # --- RECEIPT PREVIEW PANEL ---
        preview_wrapper = QFrame()
        preview_wrapper.setStyleSheet("QFrame { background-color: #f1f5f9; border-radius: 12px; }")
        preview_wrapper_layout = QVBoxLayout(preview_wrapper)
        preview_wrapper_layout.setAlignment(Qt.AlignmentFlag.AlignCenter)
        
        preview_lbl = QLabel("👀 ตัวอย่างใบเสร็จ (Live Preview)")
        preview_lbl.setStyleSheet("font-size: 16px; font-weight: bold; color: #475569; margin-bottom: 10px;")
        preview_lbl.setAlignment(Qt.AlignmentFlag.AlignCenter)
        preview_wrapper_layout.addWidget(preview_lbl)
        
        preview_frame = QFrame()
        preview_frame.setObjectName("ReceiptPaper")
        preview_frame.setStyleSheet('''
            QFrame#ReceiptPaper {
                background-color: white;
                border: 1px solid #cbd5e1;
            }
            QLabel { font-family: "Sarabun", "Cordia New", "Arial"; color: #000; }
        ''')
        preview_frame.setFixedWidth(280)
        
        shadow = QGraphicsDropShadowEffect()
        shadow.setBlurRadius(15)
        shadow.setColor(QColor(0, 0, 0, 30))
        shadow.setOffset(0, 4)
        preview_frame.setGraphicsEffect(shadow)
        
        preview_layout = QVBoxLayout(preview_frame)
        preview_layout.setContentsMargins(15, 20, 15, 20)
        preview_layout.setSpacing(4)
        
        self.preview_header = QLabel()
        self.preview_header.setAlignment(Qt.AlignmentFlag.AlignCenter)
        self.preview_header.setStyleSheet("font-size: 14px; font-weight: bold;")
        
        preview_body = QLabel("-----------------------------------\nสินค้า 1                      100.00\nสินค้า 2                       50.00\n-----------------------------------\nยอดรวมสุทธิ                 150.00\n-----------------------------------")
        preview_body.setAlignment(Qt.AlignmentFlag.AlignCenter)
        preview_body.setStyleSheet("font-size: 13px; font-family: 'Courier New', monospace;")
        
        self.preview_footer = QLabel()
        self.preview_footer.setAlignment(Qt.AlignmentFlag.AlignCenter)
        self.preview_footer.setStyleSheet("font-size: 14px;")
        
        preview_layout.addWidget(self.preview_header)
        preview_layout.addWidget(preview_body)
        preview_layout.addWidget(self.preview_footer)
        preview_layout.addStretch()
        
        preview_wrapper_layout.addWidget(preview_frame)
        
        def update_preview():
            self.preview_header.setText(self.header_edit.toPlainText())
            self.preview_footer.setText(self.footer_edit.toPlainText())
            
        self.header_edit.textChanged.connect(update_preview)
        self.footer_edit.textChanged.connect(update_preview)
        update_preview()
        
        main_layout.addWidget(settings_frame, stretch=5)
        main_layout.addWidget(preview_wrapper, stretch=4)



    def save_and_test(self):

        self.config['receipt_printer'] = self.printer_combo.currentText()

        self.config['receipt_header'] = self.header_edit.toPlainText()

        self.config['receipt_footer'] = self.footer_edit.toPlainText()

        

        parent_window = self.parent()

        if parent_window:

            parent_window.config = self.config

            parent_window.save_config()

            

            if hasattr(parent_window, 'update_receipt_ui'):

                parent_window.update_receipt_ui(self.config)

        

        printer_name = self.printer_combo.currentText()

        if not printer_name:

            QMessageBox.warning(self, "Warning", "กรุณาเลือกเครื่องพิมพ์")

            return

            

        try:
            printer_config = dict(self.config)
            parent_window = self.parent()
            if parent_window and hasattr(parent_window, 'receipt_text'):
                printer_config['_receipt_document'] = parent_window.receipt_text.document()
            print_escpos_receipt(printer_name, self.receipt_data, printer_config)

            QMessageBox.information(self, "สำเร็จ", "บันทึกและสั่งพิมพ์เรียบร้อยแล้ว!")

            self.accept()

        except Exception as e:

            QMessageBox.critical(self, "Error", f"เกิดข้อผิดพลาด:\n{str(e)}")





class ReceiptDialog(QDialog):

    def __init__(self, parent=None, receipt_data=None):

        super().__init__(parent)

        self.setWindowTitle("พิมพ์ใบเสร็จรับเงิน")

        self.setFixedWidth(440)
        self.setMinimumHeight(0)
        self.setMaximumHeight(1400)

        self.setStyleSheet("background-color: #f3f4f6; color: #111827; font-family: Tahoma, sans-serif;")

        

        self.receipt_data = receipt_data or {}

        self.init_ui()

        

    def init_ui(self):

        main_layout = QVBoxLayout(self)

        

        paper = QFrame()

        paper.setStyleSheet("background-color: white; border-radius: 8px; border: 1px solid #d1d5db;")

        self.paper_layout = QVBoxLayout(paper)
        self.paper_layout.setContentsMargins(18, 16, 18, 16)
        self.paper_layout.setSpacing(8)

        

        self.receipt_text = QTextEdit()

        self.receipt_text.setReadOnly(True)
        self.receipt_text.setFont(QFont("Tahoma", 14))
        self.receipt_text.setStyleSheet("border: none; background: transparent;")
        self.receipt_text.setFixedWidth(360)
        self.receipt_text.setLineWrapMode(QTextEdit.LineWrapMode.WidgetWidth)
        self.receipt_text.setHorizontalScrollBarPolicy(Qt.ScrollBarPolicy.ScrollBarAlwaysOff)
        self.paper_layout.setAlignment(self.receipt_text, Qt.AlignmentFlag.AlignHCenter)

        self.paper_layout.addWidget(self.receipt_text)

        

        parent_window = self.parent()

        config = getattr(parent_window, 'config', {}) if parent_window else {}

        self.update_receipt_ui(config)

        

        # Auto-open drawer if cash or split

        pm = self.receipt_data.get('payment_method', '')

        if pm in ['cash', 'split', 'เงินสด', 'ผสม (Split)']:

            printer_name = config.get('receipt_printer', '')

            if printer_name:

                try:

                    open_cash_drawer(printer_name)

                except:

                    pass



        

        top_btns = QHBoxLayout()

        print_btn = QPushButton("🖨️ พิมพ์")

        print_btn.setStyleSheet("QPushButton { background-color: #10b981; color: white; border: none; border-radius: 8px; padding: 10px; font-weight: bold; font-size: 14px; } QPushButton:hover { background-color: #059669; }")

        print_btn.clicked.connect(self.print_receipt)

        

        settings_btn = QPushButton("⚙️ ตั้งค่า")

        settings_btn.setStyleSheet("QPushButton { background-color: white; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; font-weight: bold; } QPushButton:hover { background-color: #f3f4f6; }")

        settings_btn.clicked.connect(self.open_settings)

        




        top_btns.addWidget(print_btn)

        top_btns.addWidget(settings_btn)

        self.paper_layout.addLayout(top_btns)

        new_bill_btn = QPushButton("📦 บิลใหม่")

        new_bill_btn.setStyleSheet("QPushButton { background-color: #111827; color: white; border: none; border-radius: 8px; padding: 12px; font-weight: bold; font-size: 16px; } QPushButton:hover { background-color: #374151; }")

        new_bill_btn.clicked.connect(self.accept)

        self.paper_layout.addWidget(new_bill_btn)

        

        main_layout.addWidget(paper)
        self.adjustSize()
        QTimer.singleShot(0, self._fit_receipt_layout)



    def open_settings(self):

        parent_window = self.parent()

        config = getattr(parent_window, 'config', {}) if parent_window else {}

        dialog = ReceiptSettingsDialog(self, config, self.receipt_data)

        dialog.exec()



    def update_receipt_ui(self, config):

        if not hasattr(self, 'receipt_text'): return

        

        header_text = config.get('receipt_header', '')

        if not header_text:

            header_text = "บริษัท ป๊อปสตาร์ฟู้ดส์ เทรดดิ้ง จำกัด\n480 หมู่บ้านคำเจริญ ม.1 ต.แสนสุข\nอ.วารินชำราบ จ.อุบลราชธานี 34190\nโทร. 061-935-4497 | Line: @popstarshop"

            

        footer_text = config.get('receipt_footer', '')

        if not footer_text:

            footer_text = "ขอบคุณที่ใช้บริการ\nโอกาสหน้าเชิญใหม่ครับ/ค่ะ"

        

        branch = self.receipt_data.get('branch', 'สำนักงานใหญ่')

        cashier = self.receipt_data.get('cashier', 'พนักงาน')

        receipt_no = self.receipt_data.get('doc_number', '-')

        date_str = self.receipt_data.get('date', '-')

        total_amount = _receipt_amount(self.receipt_data.get('total', 0))
        vat_rate = _receipt_amount(self.receipt_data.get('vat_rate', 7))
        vat_amount = total_amount * vat_rate / (Decimal('100') + vat_rate) if vat_rate else Decimal('0')
        before_vat = total_amount - vat_amount
        method_str = _payment_method_label(self.receipt_data.get('payment_method'))

        safe = lambda value: html.escape(str(value or ''))
        header_html = "<br>".join(safe(line) for line in header_text.splitlines())
        footer_html = "<br>".join(safe(line) for line in footer_text.splitlines())
        items_html = "".join(
            f"""
            <tr class="item-name">
                <td>{safe(item.get('name', ''))}</td>
                <td align="right">{_receipt_amount(item.get('total', 0)):,.2f}</td>
            </tr>
            <tr>
                <td colspan="2" class="item-detail">{safe(item.get('qty', 1))} x { _receipt_amount(item.get('price', 0)):,.2f}</td>
            </tr>
            """
            for item in self.receipt_data.get('items', [])
        )

        logo_path = _logo_path()
        logo_html = f'<img src="{QUrl.fromLocalFile(logo_path).toString()}" width="64">' if os.path.isfile(logo_path) else ''
        customer_html = ""
        if self.receipt_data.get('is_full_tax'):
            customer_html = f"""
                <div class="customer">
                    ลูกค้า: {safe(self.receipt_data.get('customer_name'))}<br>
                    เลขประจำตัวผู้เสียภาษี: {safe(self.receipt_data.get('customer_tax_id'))}<br>
                    ที่อยู่: {safe(self.receipt_data.get('customer_address'))}
                </div>
            """

        receipt_html = f"""
        <html><head><style>
            body {{ font-family: Tahoma, sans-serif; font-size: 13px; color: #111; margin: 0; }}
            .receipt {{ width: 100%; }}
            .center {{ text-align: center; }}
            .logo {{ margin-bottom: 2px; }}
            .shop {{ font-size: 12px; font-weight: bold; line-height: 1.2; }}
            .document {{ font-weight: bold; margin: 5px 0 3px; }}
            .meta {{ line-height: 1.35; }}
            .meta table td:last-child {{ text-align: right; }}
            .customer {{ margin-top: 4px; padding-top: 4px; line-height: 1.35; }}
            .rule {{ color: #64748b; font-family: "Courier New", monospace; font-size: 11px; line-height: 1; margin: 5px 0; }}
            table {{ width: 100%; border-collapse: collapse; }}
            td {{ padding: 2px 0; vertical-align: top; }}
            .item-name {{ padding-top: 4px; font-weight: bold; }}
            .item-detail {{ padding-left: 8px; color: #475569; }}
            .item-total, .summary td:last-child {{ text-align: right; white-space: nowrap; }}
            .summary td {{ padding: 2px 0; }}
            .grand-total {{ margin-top: 3px; padding-top: 3px; font-size: 16px; font-weight: bold; text-align: center; }}
            .footer {{ margin-top: 5px; padding-top: 5px; line-height: 1.35; }}
        </style></head><body>
            <div class="receipt">
                <div class="center logo">{logo_html}</div>
                <div class="center shop">{header_html}</div>
                <div class="center document">ใบกำกับภาษีย่อ</div>
                <div class="meta">
                    <table>
                        <tr><td>สาขา: {safe(branch)}</td><td align="right">แคชเชียร์: {safe(cashier)}</td></tr>
                        <tr><td>เลขที่: {safe(receipt_no)}</td><td align="right">{safe(_receipt_date_label(date_str))}</td></tr>
                    </table>
                </div>
                {customer_html}
                <div class="rule">----------------------------------------</div>
                <table>
                    <tbody>{items_html}</tbody>
                </table>
                <div class="rule">----------------------------------------</div>
                <table class="summary">
                    <tr><td>มูลค่าสินค้า (ก่อน VAT)</td><td align="right">{before_vat:,.2f}</td></tr>
                    <tr><td>ภาษีมูลค่าเพิ่ม {vat_rate}%</td><td align="right">{vat_amount:,.2f}</td></tr>
                </table>
                <table class="grand-total">
                    <tr><td colspan="2" class="center">ยอดสุทธิ {total_amount:,.2f} บาท</td></tr>
                </table>
                <div class="center" style="margin-top: 5px;">
                    (รวมภาษีมูลค่าเพิ่มแล้ว)<br>
                    ชำระโดย: {safe(method_str)}
                </div>
                <div class="center rule">----------------------------------------</div>
                <div class="center footer">{footer_html}</div>
            </div>
        </body></html>
        """
        document = self.receipt_text.document()
        document.setBaseUrl(QUrl.fromLocalFile(os.path.dirname(logo_path) + os.sep))
        self.receipt_text.setHtml(receipt_html)
        document.setTextWidth(self.receipt_text.width() - 20)
        content_height = document.documentLayout().documentSize().height()
        self.receipt_text.setFixedHeight(min(max(int(content_height + 36), 240), 1200))

    def _fit_receipt_layout(self):
        document = self.receipt_text.document()
        document.setTextWidth(self.receipt_text.viewport().width())
        content_height = document.documentLayout().documentSize().height()
        self.receipt_text.setFixedHeight(min(max(int(content_height + 36), 240), 1200))
        self.adjustSize()



    def manual_open_drawer(self):

        parent_window = self.parent()

        config = getattr(parent_window, 'config', {}) if parent_window else {}

        printer_name = config.get('receipt_printer', '')

        if not printer_name:

            QMessageBox.warning(self, "แจ้งเตือน", "ยังไม่ได้ตั้งค่าเครื่องพิมพ์ในหน้าตั้งค่า")

            return

        try:

            open_cash_drawer(printer_name)

        except Exception as e:

            QMessageBox.critical(self, "Error", f"ไม่สามารถเปิดลิ้นชักได้:\n{str(e)}")



    def print_receipt(self):

        parent_window = self.parent()

        config = getattr(parent_window, 'config', {}) if parent_window else {}

        

        printer_name = config.get('receipt_printer', '')

        if not printer_name:

            QMessageBox.warning(self, "Warning", "กรุณาตั้งค่าเครื่องพิมพ์ใบเสร็จ (ปุ่มตั้งค่า) ก่อน!")

            return

            

        try:

            print_escpos_receipt(
                printer_name,
                self.receipt_data,
                {**config, '_receipt_document': self.receipt_text.document()},
            )

            QMessageBox.information(self, "สำเร็จ", "สั่งพิมพ์ใบเสร็จเรียบร้อยแล้วครับ!")

        except Exception as e:

            QMessageBox.critical(self, "Error", f"เกิดข้อผิดพลาดในการพิมพ์:\n{traceback.format_exc()}")
