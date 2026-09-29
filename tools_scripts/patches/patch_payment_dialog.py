import os

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Update set_method to sync with customer_display
target_set_method = """    def set_method(self, method):
        for m, btn in self.method_btns.items():
            btn.setChecked(m == method)
        self.selected_method = method
        if hasattr(self, 'qr_label'):
            self.qr_label.hide()
        self.title_lbl.setText(f"รับชำระเงิน: {method}")"""
# Since Thai text is messy, I'll use regex.
import re
content = re.sub(
    r'(self\.selected_method = method\n\s*if hasattr\(self, \'qr_label\'\):\n\s*self\.qr_label\.hide\(\)\n\s*self\.title_lbl\.setText[^\n]+)',
    r'\1\n        parent_window = self.parent()\n        if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():\n            parent_window.customer_display.show_payment(method)',
    content
)

# Update the QR image generation in payment_dialog
qr_gen_target = """                qr_img = qrcode.make(payload).convert("RGBA")
                img_data = qr_img.tobytes("raw", "RGBA")
                qim = QImage(img_data, qr_img.size[0], qr_img.size[1], QImage.Format_RGBA8888)
                pix = QPixmap.fromImage(qim).scaled(300, 300, Qt.KeepAspectRatio, Qt.SmoothTransformation)"""

qr_gen_replace = """                import io
                qr_img = qrcode.make(payload)
                buf = io.BytesIO()
                qr_img.save(buf, format="PNG")
                pix = QPixmap()
                pix.loadFromData(buf.getvalue())
                pix = pix.scaled(300, 300, Qt.KeepAspectRatio, Qt.SmoothTransformation)"""

content = content.replace(qr_gen_target, qr_gen_replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
