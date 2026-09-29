import os
import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will replace the try/except block for Close Shift and Open Shift
target_close_shift = """                    try:
                        res = requests.post(
                            f"{self.config['api_url']}/api/pos/shift/close",
                            headers=headers,
                            json={'shift_id': self.current_shift.get('id'), 'counted_cash': dialog.counted_cash},
                            timeout=5
                        )
                        if res.status_code == 200:
                            QMessageBox.information(self, "สำเร็จ", "ปิดกะเรียบร้อย (Online)")
                            self._close_shift_local(dialog.counted_cash, synced=True)
                        else:
                            raise Exception("API Error")
                    except Exception:
                        # Fallback Offline
                        self._close_shift_local(dialog.counted_cash, synced=False)
                        QMessageBox.information(self, "สำเร็จ", "ปิดกะแบบออฟไลน์เรียบร้อย (ระบบจะส่งข้อมูลภายหลัง)")"""

replacement_close_shift = """                    try:
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
                            # DO NOT silently close offline if we can reach the server but it rejects it
                    except requests.exceptions.RequestException:
                        # Fallback Offline ONLY if network error
                        reply = QMessageBox.question(self, "Network Error", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการปิดกะแบบออฟไลน์หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                        if reply == QMessageBox.Yes:
                            self._close_shift_local(dialog.counted_cash, synced=False)
                            QMessageBox.information(self, "สำเร็จ", "ปิดกะแบบออฟไลน์เรียบร้อย")"""


target_open_shift = """                    try:
                        res = requests.post(
                            f"{self.config['api_url']}/api/pos/shift/open",
                            headers=headers,
                            json={'opening_cash': dialog.opening_cash},
                            timeout=5
                        )
                        if res.status_code == 200:
                            QMessageBox.information(self, "สำเร็จ", "เปิดกะเรียบร้อย (Online)")
                            data = res.json()
                            server_id = data.get('shift_id', 0)
                            self._open_shift_local(dialog.opening_cash, server_id=server_id, synced=True)
                        else:
                            raise Exception("API Error")
                    except Exception:
                        # Fallback Offline
                        QMessageBox.information(self, "สำเร็จ", "เปิดกะแบบออฟไลน์เรียบร้อย (ระบบจะส่งข้อมูลภายหลัง)")
                        self._open_shift_local(dialog.opening_cash, server_id=None, synced=False)"""

replacement_open_shift = """                    try:
                        res = requests.post(
                            f"{self.config['api_url']}/api/pos/shift/open",
                            headers=headers,
                            json={'opening_cash': dialog.opening_cash},
                            timeout=15
                        )
                        if res.status_code == 200:
                            QMessageBox.information(self, "สำเร็จ", "เปิดกะเรียบร้อย (Online)")
                            data = res.json()
                            server_id = data.get('shift_id', 0)
                            self._open_shift_local(dialog.opening_cash, server_id=server_id, synced=True)
                        else:
                            err_msg = res.json().get("message", res.text) if res.text else "API Error"
                            QMessageBox.warning(self, "ไม่สามารถเปิดกะ Online ได้", f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}")
                    except requests.exceptions.RequestException:
                        # Fallback Offline ONLY if network error
                        reply = QMessageBox.question(self, "Network Error", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการเปิดกะแบบออฟไลน์หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                        if reply == QMessageBox.Yes:
                            self._open_shift_local(dialog.opening_cash, server_id=None, synced=False)
                            QMessageBox.information(self, "สำเร็จ", "เปิดกะแบบออฟไลน์เรียบร้อย")"""

content = content.replace(target_close_shift, replacement_close_shift)
content = content.replace(target_open_shift, replacement_open_shift)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched shift logic!")
