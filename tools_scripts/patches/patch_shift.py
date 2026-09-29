import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(                        else:\n                            err_msg = res\.json\(\)\.get\("message", res\.text\) if res\.text else "API Error"\n                            QMessageBox\.warning\(self, ".*? Online .*?", f".*?: \{err_msg\}\\n\\n.*?"\))'

replace = r'''                        else:
                            err_msg = res.json().get("message", res.text) if res.text else "API Error"
                            reply = QMessageBox.question(self, "ข้อผิดพลาดจากเซิร์ฟเวอร์", f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\n\nคุณต้องการบังคับปิดกะในเครื่อง (Force Close) หรือไม่?", QMessageBox.Yes | QMessageBox.No)
                            if reply == QMessageBox.Yes:
                                self._close_shift_local(dialog.counted_cash, synced=True)
                                QMessageBox.information(self, "สำเร็จ", "บังคับปิดกะในเครื่องเรียบร้อยแล้ว")'''

content = re.sub(target, replace, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
