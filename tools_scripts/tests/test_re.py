import os
import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the try/except in toggle_shift for CLOSE shift
close_shift_pattern = re.compile(
    r'(# Close Shift.*?if dialog\.exec\(\) == QDialog\.Accepted:\s*# Try Online first\s*try:).*?(?=self\._close_shift_local\(dialog\.counted_cash, synced=False\)\s*QMessageBox\.information\(self, ".*?\]\)\))self\._close_shift_local\(dialog\.counted_cash, synced=False\)\s*QMessageBox\.information\(self, "[^"]+", "[^"]+"\)',
    re.DOTALL
)

close_shift_replace = r'''\1
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
                        QMessageBox.warning(self, "ไม่สามารถปิดกะ Online ได้", f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\n\nหากต้องการปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต")
                except requests.exceptions.RequestException:
                    # Fallback Offline ONLY if network error
                    reply = QMessageBox.question(self, "Network Error", "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการปิดกะแบบออฟไลน์หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                    if reply == QMessageBox.Yes:
                        self._close_shift_local(dialog.counted_cash, synced=False)
                        QMessageBox.information(self, "สำเร็จ", "ปิดกะแบบออฟไลน์เรียบร้อย")'''

# I will write a simpler script to just replace the whole toggle_shift function since it's easier and safer
