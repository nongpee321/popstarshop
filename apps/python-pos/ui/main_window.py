import sys
import os
import json
from PySide6.QtWidgets import (QApplication, QMainWindow, QWidget, QVBoxLayout, 
                               QHBoxLayout, QPushButton, QLabel, QLineEdit, 
                               QGridLayout, QScrollArea, QFrame, QTableWidget, QHeaderView,
                               QInputDialog, QMessageBox, QTableWidgetItem, QDialog)
from PySide6.QtCore import Qt, QTimer
from PySide6.QtGui import QIcon
import requests

from sync_worker import SyncWorker
from database.models import init_db, Product, Category, PosReceipt, PosReceiptItem, PosShift
from sqlalchemy.orm import Session

class MainWindow(QMainWindow):
    def __init__(self, db_path="pos.db"):
        super().__init__()
        self.db_path = db_path
        self.app_data_dir = os.path.dirname(db_path) or '.'
        self.config_file = os.path.join(self.app_data_dir, 'config.json')
        self.config = self.load_config()
        self.current_shift = None
        self.customer_display = None
        self.setWindowTitle("PopCentral POS")
        
        # Set Window Icon
        import sys
        from PySide6.QtGui import QIcon
        if getattr(sys, 'frozen', False):
            base_path = os.path.dirname(sys.executable)
        else:
            base_path = os.path.dirname(os.path.dirname(__file__))
        icon_path = os.path.join(base_path, 'assets', 'icon.ico')
        if os.path.exists(icon_path):
            self.setWindowIcon(QIcon(icon_path))
            
        self.setFixedSize(1024, 768) # Fixed size as requested
        
        self.setStyleSheet("""
            QMainWindow { background-color: #f1f5f9; }
            QLabel { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
            QPushButton { 
                font-family: 'Segoe UI'; font-weight: bold; border-radius: 6px; 
            }
        """)

        # Main Central Widget
        central_widget = QWidget()
        self.setCentralWidget(central_widget)
        main_layout = QVBoxLayout(central_widget)
        main_layout.setContentsMargins(10, 10, 10, 10)
        main_layout.setSpacing(10)
        
        # 1. Top Bar
        top_bar = self.create_top_bar()
        main_layout.addWidget(top_bar)
        
        # 2. Main Content (Left: Products, Right: Cart)
        content_layout = QHBoxLayout()
        content_layout.setSpacing(10)
        
        # Left Panel (Products)
        left_panel = self.create_products_panel()
        content_layout.addWidget(left_panel, 6) # 60% width
        
        # Right Panel (Cart)
        right_panel = self.create_cart_panel()
        content_layout.addWidget(right_panel, 4) # 40% width
        
        main_layout.addLayout(content_layout)
        
        QTimer.singleShot(500, self.check_config_and_start)
        
    def load_config(self):
        if os.path.exists(self.config_file):
            try:
                with open(self.config_file, 'r') as f:
                    return json.load(f)
            except Exception:
                pass
        return {
            'api_url': 'http://localhost:8000', 
            'pos_token': '',
            'receipt_printer': '',
            'receipt_paper_size': '58mm',
            'receipt_header': 'PopCentral POS\nบริษัท ป๊อบ เซ็นทรัล จำกัด\n480 ถนนสุคนธสวัสดิ์ เขตลาดพร้าว กรุงเทพฯ 10230\nเลขประจำตัวผู้เสียภาษี 0105566003246',
            'receipt_footer': 'ขอบคุณที่ใช้บริการ'
        }
        
    def save_config(self):
        with open(self.config_file, 'w') as f:
            json.dump(self.config, f)
            
    def check_config_and_start(self):
        if not self.config.get('pos_token'):
            url, ok1 = QInputDialog.getText(self, "ตั้งค่าเชื่อมต่อเซิร์ฟเวอร์", "กรุณาใส่ API URL (เช่น http://localhost:8000):", text=self.config.get('api_url', 'http://localhost:8000'))
            if not ok1: return
            url = url.strip()
            if url.startswith('http://') and not ('localhost' in url or '127.0.0.1' in url):
                QMessageBox.critical(self, "Security Error", "Production API ต้องใช้ https:// เท่านั้นเพื่อความปลอดภัยของระบบ!")
                return
                
            token, ok2 = QInputDialog.getText(self, "ตั้งค่า Token", "กรุณาใส่ POS Device Token:")
            if not ok2: return
            
            self.config['api_url'] = url
            self.config['pos_token'] = token.strip()
            self.save_config()
            
        self.start_sync()
        self.check_active_shift()

    def start_sync(self):
        self.sync_worker = SyncWorker(self.db_path, self.config['api_url'], self.config['pos_token'])
        self.sync_worker.sync_started.connect(self.on_sync_started)
        self.sync_worker.sync_finished.connect(self.on_sync_finished)
        self.sync_worker.products_updated.connect(self.load_products_from_db)
        self.sync_worker.start()
        
    def on_sync_started(self):
        self.network_status.setText("🟢 Syncing")
        self.network_status.setStyleSheet("font-size: 14px; font-weight: bold; color: #16a34a; background: #dcfce7; padding: 4px 10px; border-radius: 12px;")

    def on_sync_finished(self, success, message):
        if success:
            self.network_status.setText("🟢 Online")
            self.network_status.setStyleSheet("font-size: 14px; font-weight: bold; color: #16a34a; background: #dcfce7; padding: 4px 10px; border-radius: 12px;")
        else:
            self.network_status.setText("🟡 Offline")
            self.network_status.setStyleSheet("font-size: 14px; font-weight: bold; color: #ca8a04; background: #fef08a; padding: 4px 10px; border-radius: 12px;")

    def load_products_from_db(self, cat_id=None):
        session = init_db(f'sqlite:///{self.db_path}')
        
        # Reload Categories
        cats = session.query(Category).order_by(Category.id).all()
        # Clear existing category buttons
        for i in reversed(range(self.cat_layout.count())):
            widget = self.cat_layout.itemAt(i).widget()
            if widget: widget.deleteLater()
            
        all_btn = QPushButton("ทั้งหมด")
        all_btn.setToolTip("ทั้งหมด")
        all_btn.setFixedSize(100, 40)
        all_btn.setStyleSheet("QPushButton { background-color: #0284c7; color: white; border-radius: 6px; font-weight: bold; }")
        all_btn.clicked.connect(lambda checked: self.load_products_from_db(None))
        self.cat_layout.addWidget(all_btn)
        
        for cat in cats:
            btn = QPushButton(cat.name_th)
            btn.setFixedSize(100, 40)
            btn.setToolTip(cat.name_th)
            btn.setStyleSheet("QPushButton { background-color: #f1f5f9; color: #333; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: bold; } QPushButton:hover { background-color: #e2e8f0; }")
            btn.clicked.connect(lambda checked, c=cat.id: self.load_products_from_db(c))
            self.cat_layout.addWidget(btn)
        self.cat_layout.addStretch()
        
        # Reload Products
        query = session.query(Product).filter(Product.is_active == True)
        if cat_id:
            query = query.filter(Product.category_id == cat_id)
        
        products = query.order_by(Product.name_th).all()
        
        # Clear existing product buttons
        for i in reversed(range(self.prod_grid.count())):
            widget = self.prod_grid.itemAt(i).widget()
            if widget: widget.deleteLater()
            
        row, col = 0, 0
        for p in products:
            prod_btn = QPushButton(f"{p.name_th}\n\n฿{p.default_price:.2f}")
            prod_btn.setFixedSize(130, 130)
            prod_btn.setToolTip(p.name_th)
            prod_btn.setStyleSheet("""
                QPushButton { background-color: white; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: bold; }
                QPushButton:hover { border: 2px solid #0284c7; background-color: #f0f9ff; }
            """)
            prod_btn.clicked.connect(lambda checked, prod=p: self.add_to_cart(prod))
            self.prod_grid.addWidget(prod_btn, row, col)
            col += 1
            if col > 3:
                col = 0
                row += 1
                
        session.close()

    def handle_barcode_scan(self):
        query = self.search_input.text().strip()
        if not query: return
        
        try:
            db_path = os.path.join(self.app_data_dir, 'pos_offline.db')
            session = init_db(f"sqlite:///{db_path}")
            
            # Find product by exact sku_code, barcode, or exact name_th match
            product = session.query(Product).filter(
                (Product.sku_code == query) | (Product.name_th == query) | (Product.barcode == query)
            ).first()
            
            session.close()
            
            if product:
                self.add_to_cart(product)
                self.search_input.clear()
            else:
                reply = QMessageBox.question(self, "ไม่พบสินค้า", f"ไม่พบรหัสบาร์โค้ด '{query}' ในระบบ\nต้องการเพิ่มสินค้าใหม่ด่วนหรือไม่?", QMessageBox.StandardButton.Yes | QMessageBox.StandardButton.No)
                if reply == QMessageBox.StandardButton.Yes:
                    self.add_quick_product(query)
                self.search_input.selectAll()
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error scanning barcode:\n{traceback.format_exc()}")
            
    def add_quick_product(self, barcode):
        if not self.current_shift:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาเปิดกะก่อนเริ่มการขาย")
            return

        name, ok1 = QInputDialog.getText(self, "เพิ่มสินค้าใหม่ด่วน", "ชื่อสินค้า:")
        if not ok1 or not name.strip(): return
        
        price, ok2 = QInputDialog.getDouble(self, "เพิ่มสินค้าใหม่ด่วน", "ราคาขาย:", 0, 0, 99999, 2)
        if not ok2: return
        
        try:
            db_path = os.path.join(self.app_data_dir, 'pos_offline.db')
            session = init_db(f"sqlite:///{db_path}")
            
            # Create a quick local product
            import random
            new_prod = Product(
                id=-random.randint(1000000, 9999999),
                sku_code=barcode,
                name_th=name.strip(),
                default_price=price,
                pos_price=price,
                is_active=True
            )
            session.add(new_prod)
            session.commit()
            
            # Add to cart immediately
            self.add_to_cart(new_prod)
            self.search_input.clear()
            
            # Refresh products view if "All" is selected
            self.load_products_from_db()
            
            session.close()
            QMessageBox.information(self, "สำเร็จ", f"เพิ่มสินค้า {name.strip()} แล้ว (สินค้าจะเซฟไว้ขายในเครื่องนี้)")
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error adding product:\n{traceback.format_exc()}")

    def add_to_cart(self, product):
        if not self.current_shift:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาเปิดกะก่อนเริ่มการขาย")
            return
            
        # Check Stock
        stock_qty = getattr(product, 'stock_qty', None)
        
        try:
            # Check if product is already in cart
            current_cart_qty = 0
            for row in range(self.cart_table.rowCount()):
                item = self.cart_table.item(row, 0)
                if not item: continue
                item_id = item.data(Qt.UserRole)
                if item_id == product.id:
                    current_cart_qty = float(self.cart_table.item(row, 1).text())
                    
                    # Stock check removed
                    
                    # Increment quantity
                    qty = current_cart_qty + 1
                    self.cart_table.item(row, 1).setText(str(qty))
                    
                    # Update total
                    price = float(self.cart_table.item(row, 2).text())
                    self.cart_table.item(row, 3).setText(f"{qty * price:.2f}")
                    self.update_cart_total()
                    return
                    
            # Stock check removed
                
            # Add new row
            row = self.cart_table.rowCount()
            self.cart_table.insertRow(row)
            
            name_str = str(product.name_th) if product.name_th else ""
            name_item = QTableWidgetItem(name_str)
            name_item.setToolTip(name_str)
            name_item.setData(Qt.UserRole, product.id)
            
            qty_item = QTableWidgetItem("1")
            qty_item.setTextAlignment(Qt.AlignmentFlag.AlignCenter)
            
            price_val = float(product.default_price) if product.default_price else 0.0
            price_item = QTableWidgetItem(f"{price_val:.2f}")
            price_item.setTextAlignment(Qt.AlignmentFlag.AlignRight | Qt.AlignmentFlag.AlignVCenter)
            
            total_item = QTableWidgetItem(f"{price_val:.2f}")
            total_item.setTextAlignment(Qt.AlignmentFlag.AlignRight | Qt.AlignmentFlag.AlignVCenter)
            
            self.cart_table.setItem(row, 0, name_item)
            self.cart_table.setItem(row, 1, qty_item)
            self.cart_table.setItem(row, 2, price_item)
            self.cart_table.setItem(row, 3, total_item)
            
            manage_widget = QWidget()
            manage_layout = QHBoxLayout(manage_widget)
            manage_layout.setContentsMargins(0, 0, 0, 0)
            manage_layout.setSpacing(4)
            
            edit_btn = QPushButton("แก้ไข")
            edit_btn.setStyleSheet("background-color: #f59e0b; color: white; border-radius: 4px; padding: 4px 8px;")
            edit_btn.clicked.connect(self.edit_cart_item)
            
            del_btn = QPushButton("ลบ")
            del_btn.setStyleSheet("background-color: #ef4444; color: white; border-radius: 4px; padding: 4px 8px;")
            del_btn.clicked.connect(self.delete_cart_item)
            
            manage_layout.addWidget(edit_btn)
            manage_layout.addWidget(del_btn)
            self.cart_table.setCellWidget(row, 4, manage_widget)
            
            self.update_cart_total()
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error adding to cart:\n{traceback.format_exc()}")
            
    def edit_cart_item(self):
        try:
            button = self.sender()
            if not button: return
            # Find the row containing the button
            index = self.cart_table.indexAt(button.parent().pos())
            if not index.isValid(): return
            row = index.row()
            
            qty_item = self.cart_table.item(row, 1)
            if not qty_item: return
            
            current_qty = int(float(qty_item.text()))
            new_qty, ok = QInputDialog.getInt(self, "แก้ไขจำนวน", "จำนวนใหม่:", current_qty, 1, 9999, 1)
            
            if ok:
                qty_item.setText(str(new_qty))
                price_item = self.cart_table.item(row, 2)
                if price_item:
                    price = float(price_item.text())
                    total_item = self.cart_table.item(row, 3)
                    if total_item:
                        total_item.setText(f"{new_qty * price:.2f}")
                self.update_cart_total()
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error editing item:\n{traceback.format_exc()}")
            
    def delete_cart_item(self):
        try:
            button = self.sender()
            if not button: return
            index = self.cart_table.indexAt(button.parent().pos())
            if not index.isValid(): return
            row = index.row()
            self.cart_table.removeRow(row)
            self.update_cart_total()
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error deleting item:\n{traceback.format_exc()}")
        
    def update_cart_total(self):
        try:
            total = 0.0
            for row in range(self.cart_table.rowCount()):
                item = self.cart_table.item(row, 3)
                if item:
                    total += float(item.text())
            self.total_val.setText(f"฿{total:,.2f}")
            if hasattr(self, 'customer_display') and self.customer_display and not self.customer_display.isHidden():
                self.customer_display.update_cart(self.cart_table, total)
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error updating total:\n{traceback.format_exc()}")
        
    def create_top_bar(self):
        frame = QFrame()
        frame.setStyleSheet("background-color: white; border-radius: 8px; padding: 5px;")
        layout = QHBoxLayout(frame)
        layout.setContentsMargins(15, 5, 15, 5)
        
        logo = QLabel()
        import os, sys
        from PySide6.QtGui import QPixmap
        
        # Determine base path for cx_Freeze or normal execution
        if getattr(sys, 'frozen', False):
            base_path = os.path.dirname(sys.executable)
        else:
            base_path = os.path.dirname(os.path.dirname(__file__))
            
        logo_path = os.path.join(base_path, 'assets', 'logo.png')
        if os.path.exists(logo_path):
            pixmap = QPixmap(logo_path).scaledToHeight(45, Qt.SmoothTransformation)
            logo.setPixmap(pixmap)
        else:
            logo.setText("PopCentral POS")
            logo.setStyleSheet("font-size: 20px; font-weight: bold; color: #b91c1c;")
        
        version_label = QLabel("v1.10.99")
        version_label.setStyleSheet("font-size: 14px; color: #64748b; font-weight: bold; background: #e2e8f0; padding: 2px 8px; border-radius: 10px;")
        
        self.network_status = QLabel("🟡 Offline")
        self.network_status.setStyleSheet("font-size: 14px; font-weight: bold; color: #ca8a04;")
        
        # Action Buttons
        self.fs_btn = QPushButton("🔲 เต็มจอ")
        from PySide6.QtWidgets import QSizePolicy
        self.fs_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)
        self.fs_btn.setToolTip('ย่อจอ / เต็มจอ')
        self.fs_btn.setStyleSheet("""
            QPushButton { background-color: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 5px 10px; border-radius: 6px;}
            QPushButton:hover { background-color: #e2e8f0; }
        """)
        self.fs_btn.clicked.connect(self.toggle_fullscreen)
        
        self.clock_label = QLabel("00:00:00")
        self.clock_label.setStyleSheet("font-size: 16px; font-weight: bold; color: #0f172a; background: #e0f2fe; padding: 4px 10px; border-radius: 6px; border: 1px solid #bae6fd;")
        
        # Update clock every second
        self.timer = QTimer(self)
        self.timer.timeout.connect(self.update_clock)
        self.timer.start(1000)
        self.update_clock()
        
        # Cashier Button
        self.logged_in_cashier = False
        self.cashier_btn = QPushButton("👤 ยังไม่เข้าสู่ระบบ")
        self.cashier_btn.setToolTip('แคชเชียร์')
        from PySide6.QtWidgets import QSizePolicy
        self.cashier_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)
        self.cashier_btn.setStyleSheet("""
            QPushButton { background-color: #64748b; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;}
            QPushButton:hover { background-color: #475569; }
        """)
        self.cashier_btn.clicked.connect(self.login_cashier)
        
        self.logout_btn = QPushButton("🚪 ล็อกเอาท์")
        self.logout_btn.setToolTip('ออกจากระบบ')
        self.logout_btn.setStyleSheet("""
            QPushButton { background-color: #ef4444; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;}
            QPushButton:hover { background-color: #ca8a04; }
        """)
        self.logout_btn.hide()
        self.logout_btn.clicked.connect(self.logout_cashier)
        
        # Shift Button
        self.shift_btn = QPushButton("🔒 เปิดกะ (Shift Open)")
        self.shift_btn.setToolTip('จัดการกะ (Shift)')
        from PySide6.QtWidgets import QSizePolicy
        self.shift_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)
        self.shift_btn.setStyleSheet("""
            QPushButton { background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;}
            QPushButton:hover { background-color: #d97706; }
        """)
        self.shift_btn.clicked.connect(self.toggle_shift)
        
        # Hamburger Menu Button
        from PySide6.QtWidgets import QMenu
        from PySide6.QtGui import QAction
        
        menu_btn = QPushButton("☰ เมนู")
        from PySide6.QtWidgets import QSizePolicy
        menu_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)
        menu_btn.setStyleSheet("""
            QPushButton { background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: bold; font-size: 14px;}
            QPushButton:hover { background-color: #e2e8f0; }
            QPushButton::menu-indicator { image: none; }
        """)
        
        main_menu = QMenu(self)
        main_menu.setStyleSheet("""
            QMenu { background-color: white; border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px; }
            QMenu::item { padding: 8px 25px 8px 20px; font-size: 14px; font-weight: bold; color: #334155; }
            QMenu::item:selected { background-color: #f1f5f9; color: #0284c7; border-radius: 4px; }
        """)
        
        action_revenue = main_menu.addAction("ดูรายได้วันนี้")
        action_revenue.triggered.connect(self.show_today_revenue)
        
        main_menu.addSeparator()
        
        action_sync = main_menu.addAction("ซิงค์ข้อมูล (Sync Data)")
        action_sync.triggered.connect(self.manual_sync)
        
        main_menu.addSeparator()
        action_drawer = main_menu.addAction("เปิดลิ้นชัก (Open Drawer)")
        action_drawer.triggered.connect(self.manual_open_drawer)
        
        main_menu.addSeparator()
        
        action_settings = main_menu.addAction("ตั้งค่าระบบ")
        action_settings.triggered.connect(self.open_settings)
        
        menu_btn.setMenu(main_menu)
        
        layout.addWidget(logo)
        layout.addWidget(version_label)
        layout.addStretch()
        layout.addWidget(self.network_status)
        layout.addSpacing(10)
        layout.addWidget(self.fs_btn)
        layout.addSpacing(10)
        layout.addWidget(self.clock_label)
        layout.addSpacing(10)
        layout.addWidget(self.shift_btn)
        layout.addSpacing(10)
        layout.addWidget(self.cashier_btn)
        layout.addWidget(self.logout_btn)
        layout.addSpacing(10)
        self.cfd_btn = QPushButton("จอฝั่งลูกค้า")
        self.cfd_btn.setToolTip('จอฝั่งลูกค้า')
        from PySide6.QtWidgets import QSizePolicy
        self.cfd_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)
        self.cfd_btn.setStyleSheet("QPushButton { background-color: #8b5cf6; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #7c3aed; }")
        self.cfd_btn.clicked.connect(self.toggle_customer_display)
        
        layout.addWidget(self.cfd_btn)
        layout.addSpacing(10)
        layout.addWidget(menu_btn)
        
        return frame

    def toggle_customer_display(self):
        from ui.customer_display import CustomerDisplayWindow
        if not self.customer_display:
            self.customer_display = CustomerDisplayWindow(self)
        if self.customer_display.isHidden():
            self.customer_display.show()
        else:
            self.customer_display.hide()
            
    def logout_cashier(self):
        if QMessageBox.question(self, "ยืนยันการล็อกเอาท์", "คุณต้องการล็อกเอาท์ออกจากระบบแคชเชียร์ใช่หรือไม่?") == QMessageBox.Yes:
            self.logged_in_cashier = False
            self.config['cashier_name'] = ""
            self.save_config()
            self.cashier_btn.setText("👤 ยังไม่เข้าสู่ระบบ")
            self.cashier_btn.setStyleSheet("QPushButton { background-color: #64748b; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #475569; }")
            self.cashier_btn.show()
            self.logout_btn.hide()
            QMessageBox.information(self, "สำเร็จ", "ล็อกเอาท์เรียบร้อยแล้ว")

    def login_cashier(self):
        if not self.config.get('pos_token'):
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาตั้งค่า API URL และ Token ก่อนเข้าสู่ระบบ")
            return
            
        from ui.login_dialog import CashierLoginDialog
        dialog = CashierLoginDialog(self, api_url=self.config.get('api_url', ''), pos_token=self.config.get('pos_token', ''))
        if dialog.exec() == QDialog.Accepted:
            cashier = dialog.selected_cashier
            if cashier:
                name = cashier.get('name', cashier.get('code', 'Unknown'))
                self.config['cashier_name'] = name
                self.config['cashier_pin'] = getattr(dialog, 'entered_pin', '')
                self.save_config()
                self.cashier_btn.setText(f"👤   {name}")
                self.cashier_btn.setStyleSheet("QPushButton { background-color: #10b981; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #059669; }")
                self.logged_in_cashier = True
                
                # Show Logout button
                self.logout_btn.setText(f"🚪 ล็อกเอาท์ ({name})")
                self.logout_btn.show()
                self.cashier_btn.hide()
                
                QMessageBox.information(self, "สำเร็จ", f"เข้าสู่ระบบแคชเชียร์: {name} สำเร็จ")

    def toggle_shift(self):
        if not hasattr(self, 'logged_in_cashier') or not self.logged_in_cashier:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาล็อกอินเข้าระบบก่อนเปิด/ปิดกะ!")
            return
            
        headers = {
            'Authorization': f"Bearer {self.config.get('pos_token', '')}",
            'Accept': 'application/json'
        }
            
        if getattr(self, 'current_shift', None):
            # Close Shift
            from ui.shift_dialog import ShiftCloseDialog
            dialog = ShiftCloseDialog(self, expected_cash=self.current_shift.get('expected_cash', 0.0))
            if dialog.exec() == QDialog.Accepted:
                try:
                    res = requests.post(
                        f"{self.config['api_url']}/api/pos/shift/close",
                        headers=headers,
                        json={'shift_id': self.current_shift.get('id'), 'counted_cash': dialog.counted_cash},
                        timeout=15
                    )
                    if res.status_code == 200:
                        QMessageBox.information(self, "สำเร็จ", "ปิดกะเรียบร้อย (Online)")
                        self._close_shift_local(dialog.counted_cash, synced=True)
                    else:
                        err_msg = res.json().get('message', res.text) if res.text else 'API Error'
                        reply = QMessageBox.question(self, 'ข้อผิดพลาดจากเซิร์ฟเวอร์', f'เซิร์ฟเวอร์แจ้งว่า: {err_msg}\n\nคุณต้องการบังคับปิดกะในเครื่อง (Force Close) หรือไม่?', QMessageBox.Yes | QMessageBox.No)
                        if reply == QMessageBox.Yes:
                            self._close_shift_local(dialog.counted_cash, synced=True)
                            QMessageBox.information(self, 'สำเร็จ', 'บังคับปิดกะในเครื่องเรียบร้อยแล้ว')
                except requests.exceptions.RequestException:
                    reply = QMessageBox.question(self, "Network Error", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการปิดกะแบบออฟไลน์หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                    if reply == QMessageBox.Yes:
                        self._close_shift_local(dialog.counted_cash, synced=False)
                        QMessageBox.information(self, "สำเร็จ", "ปิดกะแบบออฟไลน์เรียบร้อย (ระบบจะส่งข้อมูลภายหลัง)")
        else:
            # Open Shift
            from ui.shift_dialog import ShiftOpenDialog
            dialog = ShiftOpenDialog(self, cashier_name=self.config.get('cashier_name', 'Unknown'), api_url=self.config.get('api_url', ''), pos_token=self.config.get('pos_token', ''))
            if dialog.exec() == QDialog.Accepted:
                try:
                    res = requests.post(
                        f"{self.config['api_url']}/api/pos/shift/open",
                        headers=headers,
                        json={'opening_cash': dialog.opening_cash, 'cashier': self.config.get('cashier_name', 'Unknown'), },
                        timeout=15
                    )
                    if res.status_code == 200:
                        QMessageBox.information(self, "สำเร็จ", "เปิดกะเรียบร้อย (Online)")
                        data = res.json()
                        server_id = data.get('shift_id', 0)
                        self._open_shift_local(dialog.opening_cash, server_id=server_id, synced=True)
                    else:
                        err_msg = res.json().get("message", res.text) if res.text else "API Error"
                        QMessageBox.warning(self, "ไม่สามารถเปิดกะ Online ได้", f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\n\nหากต้องการเปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต")
                except requests.exceptions.RequestException:
                    reply = QMessageBox.question(self, "Network Error", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการเปิดกะแบบออฟไลน์หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                    if reply == QMessageBox.Yes:
                        QMessageBox.information(self, "สำเร็จ", "เปิดกะแบบออฟไลน์เรียบร้อย (ระบบจะส่งข้อมูลภายหลัง)")
                        self._open_shift_local(dialog.opening_cash, server_id=None, synced=False)

    def _close_shift_local(self, counted_cash, synced=False):
        try:
            from datetime import datetime
            session = init_db(f'sqlite:///{self.db_path}')
            shift_id = self.current_shift.get('local_id')
            shift = session.query(PosShift).filter_by(id=shift_id).first() if shift_id else None
            if shift:
                shift.closed_at = datetime.utcnow()
                shift.counted_cash = counted_cash
                shift.status = 'closed'
                shift.sync_status = 'synced' if synced else 'pending'
                session.commit()
            self.current_shift = None
            self.shift_btn.setText("🔒 เปิดกะ (Shift Open)")
            self.shift_btn.setStyleSheet("QPushButton { background-color: #f59e0b; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #d97706; }")
            if hasattr(self, 'start_sync'):
                self.start_sync()
        except Exception as e:
            pass
            
    def _open_shift_local(self, opening_cash, server_id=None, synced=False):
        try:
            session = init_db(f'sqlite:///{self.db_path}')
            new_shift = PosShift(
                server_id=server_id,
                opening_cash=opening_cash,
                expected_cash=opening_cash,
                status='open',
                sync_status='synced' if synced else 'pending'
            )
            session.add(new_shift)
            session.commit()
            
            self.current_shift = {
                'id': server_id,
                'local_id': new_shift.id,
                'expected_cash': opening_cash
            }
            self.shift_btn.setText("🔓 ปิดกะ (Shift Close)")
            self.shift_btn.setStyleSheet("QPushButton { background-color: #3b82f6; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #2563eb; }")
            if hasattr(self, 'start_sync'):
                self.start_sync()
        except Exception as e:
            pass

    def check_active_shift(self):
        if not self.config.get('pos_token'): return
        try:
            # Fetch Ping for network status
            ping_res = requests.get(
                f"{self.config['api_url']}/api/pos/ping",
                headers={'Authorization': f"Bearer {self.config['pos_token']}", 'Accept': 'application/json'},
                timeout=3
            )
            # Fetch active shift
            res = requests.get(
                f"{self.config['api_url']}/api/pos/shift",
                headers={'Authorization': f"Bearer {self.config['pos_token']}", 'Accept': 'application/json'},
                timeout=3
            )
            if res.status_code == 200:
                shift = res.json().get('shift')
                if shift:
                    self.current_shift = shift
                    self.shift_btn.setText("🔓 ปิดกะ (Shift Close)")
                    self.shift_btn.setStyleSheet("QPushButton { background-color: #3b82f6; border: none; color: white; padding: 5px 10px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #2563eb; }")
        except:
            pass

    def show_today_revenue(self):
        from datetime import datetime
        session = init_db(f"sqlite:///{self.db_path}")
        receipts = session.query(PosReceipt).all()
        today_total = 0.0
        today_count = 0
        now = datetime.now()
        for r in receipts:
            dt = r.created_at
            if isinstance(dt, str):
                try:
                    dt = datetime.strptime(dt.split('.')[0], "%Y-%m-%d %H:%M:%S")
                except Exception:
                    continue
            if getattr(dt, 'day', None) == now.day and getattr(dt, 'month', None) == now.month and getattr(dt, 'year', None) == now.year:
                today_total += r.total_amount
                today_count += 1
                
        session.close()
        QMessageBox.information(self, "สรุปรายได้วันนี้", f"จำนวนบิลวันนี้: {today_count} บิล\nยอดขายรวมวันนี้: ฿ {today_total:,.2f}")


    def manual_sync(self):
        try:
            session = init_db(f'sqlite:///{self.db_path}')
            pending_receipts = session.query(PosReceipt).filter_by(sync_status='pending').count()
            pending_shifts = session.query(PosShift).filter_by(sync_status='pending').count()
            
            if pending_receipts == 0 and pending_shifts == 0:
                QMessageBox.information(self, "แจ้งเตือน", "ข้อมูลได้ถูกส่งเข้าระบบหลังบ้านเรียบร้อยเเล้วครับ")
            else:
                QMessageBox.information(self, "ซิงค์ข้อมูล", f"พบข้อมูลรอส่ง {pending_receipts + pending_shifts} รายการ กำลังเริ่มการส่งข้อมูล...")
                self.start_sync()
        except Exception as e:
            QMessageBox.warning(self, "ข้อผิดพลาด", f"ไม่สามารถตรวจสอบข้อมูลได้: {str(e)}")

    def manual_open_drawer(self):
        printer_name = self.config.get('receipt_printer', '')
        if not printer_name:
            from PySide6.QtWidgets import QMessageBox
            QMessageBox.warning(self, "แจ้งเตือน", "ไม่ได้ตั้งค่าเครื่องพิมพ์ใบเสร็จ ไม่สามารถเปิดลิ้นชักได้")
            return
            
        expected_pin = self.config.get('cashier_pin', '')
        cashier_name = self.config.get('cashier_name', '')
        if not expected_pin or cashier_name == 'Unknown' or not cashier_name:
            from PySide6.QtWidgets import QMessageBox
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาเข้าสู่ระบบแคชเชียร์ก่อนทำการเปิดลิ้นชัก (Login Required)")
            return
            
        from PySide6.QtWidgets import QInputDialog, QLineEdit, QMessageBox
        pwd, ok = QInputDialog.getText(self, "ยืนยันตัวตน", f"เปิดลิ้นชักสำหรับ {cashier_name}\nกรุณาใส่รหัสพนักงาน Cashier PIN:", QLineEdit.Password)
        if not ok:
            return
        if pwd != expected_pin:
            QMessageBox.warning(self, "ข้อผิดพลาด", "รหัสผ่านไม่ถูกต้อง!")
            return
            
        try:
            from ui.receipt_dialog import open_cash_drawer
            open_cash_drawer(printer_name)
        except Exception as e:
            error_msg = str(e).replace(self.config.get('pos_token', ''), '***TOKEN_HIDDEN***')
            QMessageBox.critical(self, "Error", f"ไม่สามารถเชื่อมต่อเครื่องพิมพ์ได้:\n{error_msg}")

    def open_settings(self):
        url, ok1 = QInputDialog.getText(self, "ตั้งค่าเชื่อมต่อเซิร์ฟเวอร์", "กรุณาใส่ API URL:", text=self.config.get('api_url', 'http://localhost:8000'))
        if not ok1: return
        url = url.strip()
        if url.startswith('http://') and not ('localhost' in url or '127.0.0.1' in url):
            QMessageBox.critical(self, "Security Error", "Production API ต้องใช้ https:// เท่านั้นเพื่อความปลอดภัยของระบบ!")
            return
            
        token, ok2 = QInputDialog.getText(self, "ตั้งค่า Token", "กรุณาใส่ POS Device Token:", text=self.config.get('pos_token', ''))
        if not ok2: return
        
        self.config['api_url'] = url
        self.config['pos_token'] = token.strip()
        self.save_config()
        
        # Stop existing worker if running
        if hasattr(self, 'sync_worker'):
            self.sync_worker.stop()
            
        # Restart sync
        self.start_sync()
        QMessageBox.information(self, "สำเร็จ", "บันทึกการตั้งค่าเรียบร้อย ระบบกำลังซิงค์ข้อมูลใหม่")

    def create_products_panel(self):
        frame = QFrame()
        frame.setStyleSheet("background-color: white; border-radius: 8px;")
        layout = QVBoxLayout(frame)
        
        # Search Bar
        search_layout = QHBoxLayout()
        self.search_input = QLineEdit()
        self.search_input.setPlaceholderText("พิมพ์ชื่อ, บาร์โค้ด หรือสแกนบาร์โค้ดที่นี่...")
        self.search_input.setStyleSheet("padding: 10px; font-size: 16px; border: 1px solid #cbd5e1; border-radius: 6px; color: black;")
        self.search_input.returnPressed.connect(self.handle_barcode_scan)
        
        search_btn = QPushButton("ค้นหา")
        search_btn.setStyleSheet("background-color: #0284c7; color: white; padding: 10px 20px; font-size: 16px; border-radius: 6px; font-weight: bold;")
        search_btn.clicked.connect(self.handle_barcode_scan)
        
        search_layout.addWidget(self.search_input)
        search_layout.addWidget(search_btn)
        layout.addLayout(search_layout)
        
        # Categories
        cat_scroll = QScrollArea()
        cat_scroll.setWidgetResizable(True)
        cat_scroll.setFixedHeight(75)
        cat_scroll.setStyleSheet("QScrollArea { border: none; background: transparent; } QWidget#catWidget { background: transparent; }")
        cat_scroll.setVerticalScrollBarPolicy(Qt.ScrollBarAlwaysOff)
        
        cat_widget = QWidget()
        cat_widget.setObjectName("catWidget")
        self.cat_layout = QHBoxLayout(cat_widget)
        self.cat_layout.setContentsMargins(0, 0, 0, 0)
        self.cat_layout.setSpacing(10)
        
        cat_scroll.setWidget(cat_widget)
        layout.addWidget(cat_scroll)
        
        # Products Grid
        prod_scroll = QScrollArea()
        prod_scroll.setWidgetResizable(True)
        prod_scroll.setStyleSheet("border: none; background: #f8fafc;")
        
        prod_container = QWidget()
        self.prod_grid = QGridLayout(prod_container)
        self.prod_grid.setSpacing(10)
        self.prod_grid.setContentsMargins(0, 10, 0, 0)
        
        prod_scroll.setWidget(prod_container)
        layout.addWidget(prod_scroll)
        
        return frame

    def create_cart_panel(self):
        frame = QFrame()
        frame.setStyleSheet("background-color: white; border-radius: 8px;")
        layout = QVBoxLayout(frame)
        layout.setContentsMargins(15, 15, 15, 15)
        
        title = QLabel("ตะกร้าสินค้า")
        title.setStyleSheet("font-size: 18px; font-weight: bold; color: #0f172a;")
        layout.addWidget(title)
        
        # Cart Table
        self.cart_table = QTableWidget(0, 5)
        self.cart_table.setHorizontalHeaderLabels(["รายการ", "จำนวน", "ราคา", "รวม", "จัดการ"])
        self.cart_table.horizontalHeader().setSectionResizeMode(0, QHeaderView.Stretch)
        self.cart_table.horizontalHeader().setSectionResizeMode(1, QHeaderView.ResizeToContents)
        self.cart_table.horizontalHeader().setSectionResizeMode(2, QHeaderView.ResizeToContents)
        self.cart_table.horizontalHeader().setSectionResizeMode(3, QHeaderView.ResizeToContents)
        self.cart_table.horizontalHeader().setSectionResizeMode(4, QHeaderView.ResizeToContents)
        self.cart_table.setStyleSheet("""
            QTableWidget {
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                background: white;
                color: #0f172a;
            }
            QTableWidget::item {
                padding: 4px;
            }
            QTableWidget::item:selected {
                background-color: #e2e8f0;
                color: #0f172a;
            }
            QHeaderView::section {
                background-color: #f8fafc;
                padding: 8px;
                border: none;
                border-bottom: 1px solid #cbd5e1;
                font-weight: bold;
                color: #475569;
            }
        """)
        self.cart_table.setEditTriggers(QTableWidget.NoEditTriggers)
        layout.addWidget(self.cart_table)
        
        # Totals
        total_frame = QFrame()
        total_frame.setStyleSheet("background-color: #f8fafc; border-radius: 8px; border: 1px solid #cbd5e1;")
        total_layout = QHBoxLayout(total_frame)
        
        total_label = QLabel("ยอดรวมทั้งสิ้น:")
        total_label.setStyleSheet("font-size: 20px; font-weight: bold; color: #334155;")
        self.total_val = QLabel("฿0.00")
        self.total_val.setStyleSheet("font-size: 28px; font-weight: bold; color: #b91c1c;")
        self.total_val.setAlignment(Qt.AlignmentFlag.AlignRight | Qt.AlignmentFlag.AlignVCenter)
        
        total_layout.addWidget(total_label)
        total_layout.addWidget(self.total_val)
        layout.addWidget(total_frame)
        
        # Payment Buttons
        btn_layout = QHBoxLayout()
        
        cancel_bill_btn = QPushButton("ยกเลิกบิล")
        cancel_bill_btn.setToolTip('ยกเลิกบิลปัจจุบัน')
        cancel_bill_btn.setStyleSheet("background-color: #ef4444; color: white; border-radius: 8px; padding: 15px; font-size: 24px; font-weight: bold;")
        cancel_bill_btn.clicked.connect(self.cancel_current_bill)
        
        pay_btn = QPushButton("ชำระเงิน")
        pay_btn.setToolTip('ชำระเงิน')
        pay_btn.setStyleSheet("background-color: #10b981; color: white; border-radius: 8px; padding: 15px; font-size: 24px; font-weight: bold;")
        pay_btn.clicked.connect(self.show_payment_dialog)
        
        btn_layout.addWidget(cancel_bill_btn)
        btn_layout.addWidget(pay_btn)
        layout.addLayout(btn_layout)
        
        return frame
        
    def keyPressEvent(self, event):
        # If user starts typing, automatically focus search_input
        if event.text() and not self.search_input.hasFocus():
            self.search_input.setFocus()
            self.search_input.setText(self.search_input.text() + event.text())
        super().keyPressEvent(event)
        

    def cancel_current_bill(self):
        if self.cart_table.rowCount() == 0:
            QMessageBox.warning(self, "แจ้งเตือน", "ไม่มีสินค้าในตะกร้า!")
            return
            
        reason, ok = QInputDialog.getText(self, "ยกเลิกบิล (ล้างตะกร้า)", "กรุณาระบุเหตุผลการยกเลิกบิล:")
        if not ok or not reason.strip():
            return
            
        try:
            total_amount = 0.0
            items_list = []
            for row in range(self.cart_table.rowCount()):
                name = self.cart_table.item(row, 0).text()
                qty = float(self.cart_table.item(row, 1).text())
                price = float(self.cart_table.item(row, 2).text())
                total = float(self.cart_table.item(row, 3).text())
                total_amount += total
                items_list.append({"name": name, "qty": qty, "price": price, "total": total})
                
            db_path = os.path.join(self.app_data_dir, 'pos_offline.db')
            session = init_db(f"sqlite:///{db_path}")
            
            from database.models import CancelledBill
            import json
            
            cancel_record = CancelledBill(
                shift_id=self.current_shift.get('id') if self.current_shift else None,
                reason=reason.strip(),
                total_amount=total_amount,
                items_json=json.dumps(items_list)
            )
            session.add(cancel_record)
            session.commit()
            session.close()
            
            # Clear cart
            self.cart_table.setRowCount(0)
            self.update_cart_total()
            QMessageBox.information(self, "สำเร็จ", "ยกเลิกบิลและบันทึกเหตุผลเรียบร้อยแล้ว")
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error cancelling bill:\n{traceback.format_exc()}")

    def show_payment_dialog(self):
        try:
            if not self.current_shift:
                QMessageBox.warning(self, "แจ้งเตือน", "กรุณาเปิดกะก่อนทำการชำระเงิน")
                return
                
            if self.cart_table.rowCount() == 0:
                QMessageBox.warning(self, "แจ้งเตือน", "ไม่มีสินค้าในตะกร้า!")
                return
                
            total_amount = 0.0
            total_items = self.cart_table.rowCount()
            total_qty = 0.0
            
            for row in range(total_items):
                qty_item = self.cart_table.item(row, 1)
                total_item = self.cart_table.item(row, 3)
                if qty_item and total_item:
                    qty = float(qty_item.text())
                    total = float(total_item.text().replace(',', ''))
                    total_qty += qty
                    total_amount += total
                
            from ui.payment_dialog import PaymentDialog
            dialog = PaymentDialog(self, total_amount, total_items, total_qty)
            if dialog.exec() == QDialog.Accepted:
                self.process_payment(
                    method=dialog.selected_method,
                    received_amount=dialog.received_amount,
                    change_amount=dialog.change_amount,
                    cash_amount=dialog.cash_amount,
                    transfer_amount=dialog.transfer_amount,
                    is_full_tax=dialog.is_full_tax,
                    customer_info={
                        'name': dialog.customer_name,
                        'tax_id': dialog.customer_tax_id,
                        'address': dialog.customer_address
                    }
                )
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error in show_payment_dialog", str(e) + "\n" + traceback.format_exc())

    def process_payment(self, method, received_amount, change_amount, cash_amount=0, transfer_amount=0, is_full_tax=False, customer_info=None):
        try:
            import uuid
            from datetime import datetime
            import json
            
            db_path = os.path.join(self.app_data_dir, 'pos_offline.db')
            session = init_db(f"sqlite:///{db_path}")
            
            receipt_id = str(uuid.uuid4())
            total_amount = 0.0
            
            method_map = {
                'เงินสด': 'cash',
                'QR': 'transfer',
                'ผสม (Split)': 'mixed',
                'บัตรเครดิต': 'card',
                'เช็ค': 'check'
            }
            
            if method == 'เงินสด':
                cash_amount = received_amount - change_amount
                transfer_amount = 0
            elif method == 'QR':
                transfer_amount = received_amount
                cash_amount = 0
                
            cust_name = customer_info.get('name') if customer_info else None
            cust_tax = customer_info.get('tax_id') if customer_info else None
            cust_addr = customer_info.get('address') if customer_info else None
            
            # Note: We will save total_amount below after loop
            
            items = []
            for row in range(self.cart_table.rowCount()):
                name_item = self.cart_table.item(row, 0)
                qty_item = self.cart_table.item(row, 1)
                price_item = self.cart_table.item(row, 2)
                
                if not name_item or not qty_item or not price_item:
                    continue
                    
                product_id = name_item.data(Qt.UserRole)
                qty = float(qty_item.text())
                price = float(price_item.text())
                total = qty * price
                total_amount += total
                
                items.append(PosReceiptItem(
                    receipt_id=receipt_id,
                    product_id=product_id,
                    sku_code='OFFLINE',
                    name=name_item.text(),
                    qty=qty,
                    unit_price=price,
                    total_price=total
                ))
                
            doc_no = 'OFFLINE-' + receipt_id[:8].upper()
            
            new_receipt = PosReceipt(
                id=receipt_id,
                shift_id=self.current_shift.get('local_id') if getattr(self, 'current_shift', None) else None,
                doc_number=doc_no,
                total_amount=total_amount,
                received_amount=received_amount,
                payment_method=method_map.get(method, 'cash'),
                cash_amount=cash_amount,
                transfer_amount=transfer_amount,
                is_full_tax=is_full_tax,
                customer_name=cust_name,
                customer_tax_id=cust_tax,
                customer_address=cust_addr,
                sync_status='pending'
            )
            
            session.add(new_receipt)
            session.add_all(items)
            
            # Extract data before commit expires the instances
            receipt_items_data = [{'name': i.name, 'qty': i.qty, 'price': i.unit_price, 'total': i.total_price} for i in items]
            
            session.commit()
            session.close()
            
            self.cart_table.setRowCount(0)
            self.update_cart_total()
            
            # Show Receipt Dialog
            from datetime import datetime
            receipt_data = {
                'doc_number': doc_no,
                'total': total_amount,
                'payment_method': method_map.get(method, 'cash'),
                'is_full_tax': is_full_tax,
                'customer_name': cust_name,
                'customer_tax_id': cust_tax,
                'customer_address': cust_addr,
                'cashier': self.config.get('cashier_name', 'Unknown'),
                'branch': self.config.get('branch_name', ''),
                'date': datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                'items': receipt_items_data
            }
            from ui.receipt_dialog import ReceiptDialog
            receipt_dialog = ReceiptDialog(self, receipt_data=receipt_data)
            receipt_dialog.exec()
            
        except Exception as e:
            import traceback
            QMessageBox.critical(self, "Error", f"Error saving receipt:\n{traceback.format_exc()}")
        
    def update_clock(self):
        from datetime import datetime
        self.clock_label.setText(datetime.now().strftime("%H:%M:%S"))

    def toggle_fullscreen(self):
        if self.isFullScreen():
            self.showNormal()
            self.fs_btn.setText("🔲 เต็มจอ")
        else:
            self.showFullScreen()
            self.fs_btn.setText("🔲 ย่อจอ")

if __name__ == "__main__":
    app = QApplication(sys.argv)
    window = MainWindow()
    window.show()
    sys.exit(app.exec())
