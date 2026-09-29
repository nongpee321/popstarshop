import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix network_status text
content = content.replace('"🔴 Offline (Error)"', '"🟡 Offline ON"')
content = content.replace('"🔴 Offline"', '"🟡 Offline ON"')
content = content.replace('#dc2626; background: #fee2e2;', '#ca8a04; background: #fef08a;') # Yellow background
content = content.replace('color: #dc2626;', 'color: #ca8a04;')

# Rewrite toggle_shift for Dual Mode (Online first, fallback to Offline)
match = re.search(r'def toggle_shift\(self\):.*?def check_active_shift\(self\):', content, re.DOTALL)
if match:
    old_body = match.group(0)
    new_body = '''def toggle_shift(self):
        if not hasattr(self, 'logged_in_cashier') or not self.logged_in_cashier:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาเข้าสู่ระบบก่อนเปิด/ปิดกะ!")
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
                # Try Online first
                try:
                    res = requests.post(
                        f"{self.config['api_url']}/api/pos/shift/close",
                        headers=headers,
                        json={'shift_id': self.current_shift.get('id'), 'counted_cash': dialog.counted_cash},
                        timeout=5
                    )
                    if res.status_code == 200:
                        QMessageBox.information(self, "สำเร็จ", "ปิดกะแบบออนไลน์เรียบร้อย")
                        self._close_shift_local(dialog.counted_cash, synced=True)
                    else:
                        raise Exception("API Error")
                except Exception:
                    # Fallback Offline
                    self._close_shift_local(dialog.counted_cash, synced=False)
                    QMessageBox.information(self, "สำเร็จ", "ปิดกะแบบออฟไลน์เรียบร้อย (ระบบจะส่งข้อมูลภายหลัง)")
        else:
            # Open Shift
            from ui.shift_dialog import ShiftOpenDialog
            dialog = ShiftOpenDialog(self, cashier_name=self.config.get('cashier_name', 'Unknown'), api_url=self.config.get('api_url', ''), pos_token=self.config.get('pos_token', ''))
            if dialog.exec() == QDialog.Accepted:
                # Try Online first
                try:
                    res = requests.post(
                        f"{self.config['api_url']}/api/pos/shift/open",
                        headers=headers,
                        json={'opening_cash': dialog.opening_cash, 'cashier': self.config.get('cashier_name', 'Unknown'), 'branch': 'สาขา 6'},
                        timeout=5
                    )
                    if res.status_code == 200:
                        QMessageBox.information(self, "สำเร็จ", "เปิดกะแบบออนไลน์เรียบร้อย")
                        data = res.json()
                        server_id = data.get('shift_id', 0)
                        self._open_shift_local(dialog.opening_cash, server_id=server_id, synced=True)
                    else:
                        raise Exception("API Error")
                except Exception:
                    # Fallback Offline
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
            self.shift_btn.setText("🔒 เปิดกะการขาย")
            self.shift_btn.setStyleSheet("QPushButton { background-color: #f59e0b; border: none; color: white; padding: 5px 15px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #d97706; }")
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
            self.shift_btn.setText("🔓 ปิดกะ (สาขา: 6)")
            self.shift_btn.setStyleSheet("QPushButton { background-color: #3b82f6; border: none; color: white; padding: 5px 15px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #2563eb; }")
            if hasattr(self, 'start_sync'):
                self.start_sync()
        except Exception as e:
            pass

    def check_active_shift(self):'''
    
    content = content.replace(old_body, new_body)
    with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Toggle Shift replaced successfully!")
else:
    print("Could not find toggle_shift body.")
