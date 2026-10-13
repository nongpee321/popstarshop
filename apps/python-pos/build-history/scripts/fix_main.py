import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will replace the open_settings method completely
open_settings_replacement = '''    def open_settings(self):
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

    def create_products_panel(self):'''

content = re.sub(r'    def open_settings\(self\):.*?    def create_products_panel\(self\):', open_settings_replacement, content, flags=re.DOTALL)

# Let's also check check_config_and_start to fix any bad indentation
check_config_replacement = '''    def check_config_and_start(self):
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

    def start_sync(self):'''

content = re.sub(r'    def check_config_and_start\(self\):.*?    def start_sync\(self\):', check_config_replacement, content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed indentation")
