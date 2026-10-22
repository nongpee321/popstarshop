import traceback
import json
import sqlite3
from PySide6.QtWidgets import (
    QDialog, QVBoxLayout, QHBoxLayout, QLabel, 
    QPushButton, QTableWidget, QTableWidgetItem, QHeaderView, QMessageBox, QFrame
)
from PySide6.QtCore import Qt

class PendingSyncDialog(QDialog):
    """
    หน้าต่างตรวจสอบรายการบิลค้าง Sync:
    - แสดงรายการบิลที่รอส่งขึ้น ERP หรือบิลที่ติดปัญหา
    - แสดงเลขที่บิล วันที่ เวลา ยอดเงิน สถานะ และสาเหตุที่ส่งไม่ผ่าน
    - มีปุ่มสั่ง Sync บิลทั้งหมดทันที
    """
    def __init__(self, parent=None, db_path="pos.db"):
        super().__init__(parent)
        self.parent_window = parent
        self.db_path = db_path
        
        self.setWindowTitle("⏳ รายการบิลค้าง Sync (Offline / Pending Sales)")
        self.resize(750, 480)
        self.setStyleSheet("""
            QDialog { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, sans-serif; }
            QLabel { color: #1e293b; font-size: 14px; }
            QTableWidget {
                background: white; border: 1px solid #cbd5e1; border-radius: 8px;
                gridline-color: #f1f5f9; font-size: 13.5px;
            }
            QHeaderView::section {
                background-color: #f1f5f9; padding: 8px; border: none;
                border-bottom: 1px solid #cbd5e1; font-weight: bold; color: #475569;
            }
        """)
        
        self.setup_ui()
        self.load_pending_sales()

    def setup_ui(self):
        layout = QVBoxLayout(self)
        layout.setSpacing(12)
        layout.setContentsMargins(18, 18, 18, 18)

        # Header Box
        header_frame = QFrame()
        header_frame.setStyleSheet("background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); border-radius: 10px; padding: 12px;")
        h_layout = QVBoxLayout(header_frame)
        h_layout.setContentsMargins(10, 8, 10, 8)
        
        title_lbl = QLabel("⏳ ตรวจสอบรายการบิลค้างส่งเข้า ERP")
        title_lbl.setStyleSheet("color: white; font-size: 17px; font-weight: bold;")
        self.subtitle_lbl = QLabel("กำลังโหลดข้อมูลบิลค้าง...")
        self.subtitle_lbl.setStyleSheet("color: #fee2e2; font-size: 13px;")
        h_layout.addWidget(title_lbl)
        h_layout.addWidget(self.subtitle_lbl)
        layout.addWidget(header_frame)

        # Table
        self.table = QTableWidget(0, 6)
        self.table.setHorizontalHeaderLabels(["เลขที่บิล", "วันที่/เวลา", "ยอดรวม", "วิธีชำระ", "สถานะ", "สาเหตุ/ข้อผิดพลาด"])
        self.table.horizontalHeader().setSectionResizeMode(0, QHeaderView.ResizeToContents)
        self.table.horizontalHeader().setSectionResizeMode(1, QHeaderView.ResizeToContents)
        self.table.horizontalHeader().setSectionResizeMode(2, QHeaderView.ResizeToContents)
        self.table.horizontalHeader().setSectionResizeMode(3, QHeaderView.ResizeToContents)
        self.table.horizontalHeader().setSectionResizeMode(4, QHeaderView.ResizeToContents)
        self.table.horizontalHeader().setSectionResizeMode(5, QHeaderView.Stretch)
        self.table.setEditTriggers(QTableWidget.NoEditTriggers)
        self.table.setSelectionBehavior(QTableWidget.SelectRows)
        layout.addWidget(self.table)

        # Bottom Actions
        btn_layout = QHBoxLayout()
        self.sync_now_btn = QPushButton("🔄 สั่ง Sync ทันที (Retry Sync)")
        self.sync_now_btn.setStyleSheet("background-color: #0284c7; color: white; border-radius: 8px; padding: 10px 18px; font-size: 14px; font-weight: bold;")
        self.sync_now_btn.clicked.connect(self.trigger_sync_now)

        close_btn = QPushButton("ปิดหน้าต่าง")
        close_btn.setStyleSheet("background-color: #e2e8f0; color: #475569; border-radius: 8px; padding: 10px 18px; font-size: 14px; font-weight: bold;")
        close_btn.clicked.connect(self.accept)

        btn_layout.addWidget(self.sync_now_btn)
        btn_layout.addStretch()
        btn_layout.addWidget(close_btn)
        layout.addLayout(btn_layout)

    def load_pending_sales(self):
        try:
            con = sqlite3.connect(self.db_path)
            cur = con.cursor()
            cur.execute("""
                SELECT local_sale_id, doc_number, created_at, total_amount, payment_method, status, sync_error
                FROM sales
                WHERE status IN ('pending', 'failed')
                ORDER BY created_at DESC;
            """)
            rows = cur.fetchall()
            con.close()

            self.table.setRowCount(0)
            for row_idx, r in enumerate(rows):
                self.table.insertRow(row_idx)
                
                doc_no = str(r[1] or r[0][:13])
                dt = str(r[2] or '-')[:19]
                amt = f"฿{float(r[3] or 0):,.2f}"
                pm = "เงินสด" if r[4] == 'cash' else ("โอน/QR" if r[4] == 'transfer' else str(r[4] or '-'))
                st = "รอส่ง" if r[5] == 'pending' else "ส่งไม่ผ่าน"
                
                err_text = ""
                if r[6]:
                    try:
                        err_obj = json.loads(r[6])
                        err_text = err_obj.get('message', r[6]) if isinstance(err_obj, dict) else str(r[6])
                    except Exception:
                        err_text = str(r[6])

                self.table.setItem(row_idx, 0, QTableWidgetItem(doc_no))
                self.table.setItem(row_idx, 1, QTableWidgetItem(dt))
                amt_item = QTableWidgetItem(amt)
                amt_item.setTextAlignment(Qt.AlignmentFlag.AlignRight | Qt.AlignmentFlag.AlignVCenter)
                self.table.setItem(row_idx, 2, amt_item)
                self.table.setItem(row_idx, 3, QTableWidgetItem(pm))
                
                st_item = QTableWidgetItem(st)
                st_item.setTextAlignment(Qt.AlignmentFlag.AlignCenter)
                if r[5] == 'failed':
                    st_item.setForeground(Qt.red)
                else:
                    st_item.setForeground(Qt.darkYellow)
                self.table.setItem(row_idx, 4, st_item)
                
                self.table.setItem(row_idx, 5, QTableWidgetItem(err_text))

            count = len(rows)
            if count > 0:
                self.subtitle_lbl.setText(f"พบบิลค้าง Sync จำนวน {count} บิล (ระบบจะพยายามส่งซ้ำอัตโนมัติ)")
            else:
                self.subtitle_lbl.setText("🎉 ไม่มีบิลค้างส่ง — บิลทั้งหมดถูกบันทึกขึ้น ERP เรียบร้อยสมบูรณ์แล้ว")
        except Exception as e:
            self.subtitle_lbl.setText(f"เกิดข้อผิดพลาดในการอ่านข้อมูล: {e}")

    def trigger_sync_now(self):
        try:
            if hasattr(self.parent_window, 'sync_worker') and self.parent_window.sync_worker:
                # รีเซ็ตสถานะบิลที่ failed ให้กลับเป็น pending เพื่อส่งใหม่
                con = sqlite3.connect(self.db_path)
                cur = con.cursor()
                cur.execute("UPDATE outbox SET status = 'pending', retry_count = 0 WHERE status = 'failed';")
                cur.execute("UPDATE sales SET status = 'pending' WHERE status = 'failed';")
                con.commit()
                con.close()

                # สั่ง Sync ทันที
                self.parent_window.sync_worker._sync_outbox_sales()
                pending_count = self.parent_window.sync_worker.get_pending_count()
                self.parent_window.update_pending_sync_badge(pending_count)
                
                self.load_pending_sales()
                QMessageBox.information(self, "ส่งข้อมูลแล้ว", "ระบบได้ส่งบิลค้างขึ้น ERP เรียบร้อยแล้ว")
            else:
                QMessageBox.warning(self, "แจ้งเตือน", "ระบบ Sync Background ไม่ได้ทำงานอยู่")
        except Exception as e:
            QMessageBox.critical(self, "Error", f"เกิดข้อผิดพลาด: {e}")
