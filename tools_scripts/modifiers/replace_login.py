import re
with open(r'D:\pop-erp-food\python-pos\ui\login_dialog.py', 'r', encoding='utf-8') as f:
    content = f.read()

match = re.search(r'try:\s*res = requests\.post.*?finally:', content, re.DOTALL)
if match:
    old_try = match.group(0)
    new_try = '''try:
            res = requests.post(
                f"{self.api_url}/api/pos/cashier/login",
                headers={'Authorization': f"Bearer {self.pos_token}", 'Accept': 'application/json'},
                json={'code': code, 'pin': pin},
                timeout=5
            )
            res.encoding = 'utf-8'
            data = res.json()
            if res.status_code == 200 and data.get('success'):
                self.selected_cashier = data.get('cashier', {})
                self.accept()
            else:
                msg = data.get('message', 'รหัสพนักงานหรือรหัสผ่านไม่ถูกต้อง')
                QMessageBox.warning(self, "เข้าสู่ระบบล้มเหลว", msg)
        except Exception as e:
            # Fallback to Offline Login
            reply = QMessageBox.question(self, "โหมดออฟไลน์", f"ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ ต้องการเข้าสู่ระบบแบบออฟไลน์ด้วยรหัส {code} หรือไม่?", QMessageBox.Yes | QMessageBox.No)
            if reply == QMessageBox.Yes:
                self.selected_cashier = {'code': code, 'name': f"พนักงาน (ออฟไลน์: {code})"}
                self.accept()
        finally:'''
    content = content.replace(old_try, new_try)
    with open(r'D:\pop-erp-food\python-pos\ui\login_dialog.py', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Login Offline Fallback added!")
else:
    print("Could not find try block in login.")
