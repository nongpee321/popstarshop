import os
import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

new_toggle_shift = """    def toggle_shift(self):
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
                        err_msg = res.json().get("message", res.text) if res.text else "API Error"
                        QMessageBox.warning(self, "ไม่สามารถปิดกะ Online ได้", f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\\n\\nหากต้องการปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต")
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
                        json={'opening_cash': dialog.opening_cash, 'cashier': self.config.get('cashier_name', 'Unknown'), 'branch': 'สาขา 6'},
                        timeout=15
                    )
                    if res.status_code == 200:
                        QMessageBox.information(self, "สำเร็จ", "เปิดกะเรียบร้อย (Online)")
                        data = res.json()
                        server_id = data.get('shift_id', 0)
                        self._open_shift_local(dialog.opening_cash, server_id=server_id, synced=True)
                    else:
                        err_msg = res.json().get("message", res.text) if res.text else "API Error"
                        QMessageBox.warning(self, "ไม่สามารถเปิดกะ Online ได้", f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\\n\\nหากต้องการเปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต")
                except requests.exceptions.RequestException:
                    reply = QMessageBox.question(self, "Network Error", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการเปิดกะแบบออฟไลน์หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                    if reply == QMessageBox.Yes:
                        QMessageBox.information(self, "สำเร็จ", "เปิดกะแบบออฟไลน์เรียบร้อย (ระบบจะส่งข้อมูลภายหลัง)")
                        self._open_shift_local(dialog.opening_cash, server_id=None, synced=False)"""

content = re.sub(r'    def toggle_shift\(self\):.*?    def _close_shift_local\(self, counted_cash, synced=False\):', new_toggle_shift + '\n\n    def _close_shift_local(self, counted_cash, synced=False):', content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("toggle_shift patched successfully!")
