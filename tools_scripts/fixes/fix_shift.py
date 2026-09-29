with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

in_toggle = False
for i, line in enumerate(lines):
    if 'def toggle_shift' in line:
        in_toggle = True
    if in_toggle and 'err_msg = res.json().get(' in line:
        # We found the err_msg definition line inside the else block
        lines[i] = "                        err_msg = res.json().get('message', res.text) if res.text else 'API Error'\n"
        lines[i+1] = "                        reply = QMessageBox.question(self, 'ข้อผิดพลาดจากเซิร์ฟเวอร์', f'เซิร์ฟเวอร์แจ้งว่า: {err_msg}\\n\\nคุณต้องการบังคับปิดกะในเครื่อง (Force Close) หรือไม่?', QMessageBox.Yes | QMessageBox.No)\n"
        lines.insert(i+2, "                        if reply == QMessageBox.Yes:\n                            self._close_shift_local(dialog.counted_cash, synced=True)\n                            QMessageBox.information(self, 'สำเร็จ', 'บังคับปิดกะในเครื่องเรียบร้อยแล้ว')\n")
        print("Fixed force close")
        break

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
