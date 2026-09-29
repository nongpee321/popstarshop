import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add action to menu
menu_code_old = '''        action_revenue.triggered.connect(self.show_today_revenue)
        
        main_menu.addSeparator()
        
        action_settings = main_menu.addAction("ตั้งค่าเชื่อมต่อระบบ")'''
        
menu_code_new = '''        action_revenue.triggered.connect(self.show_today_revenue)
        
        main_menu.addSeparator()
        
        action_sync = main_menu.addAction("ซิงค์ข้อมูล (Sync Data)")
        action_sync.triggered.connect(self.manual_sync)
        
        main_menu.addSeparator()
        
        action_settings = main_menu.addAction("ตั้งค่าเชื่อมต่อระบบ")'''

content = content.replace(menu_code_old, menu_code_new)

# 2. Add manual_sync method
manual_sync_code = '''
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

    def open_settings(self):'''

content = content.replace('    def open_settings(self):', manual_sync_code)

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)
