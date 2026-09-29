import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """                qr_img = qrcode.make(payload).convert("RGBA")
                qim = QImage(qr_img.tobytes("raw", "RGBA"), qr_img.size[0], qr_img.size[1], QImage.Format_RGBA8888)
                pix = QPixmap.fromImage(qim).scaled(300, 300, Qt.KeepAspectRatio, Qt.SmoothTransformation)"""

replacement = """                qr_img = qrcode.make(payload).convert("RGBA")
                img_data = qr_img.tobytes("raw", "RGBA")
                qim = QImage(img_data, qr_img.size[0], qr_img.size[1], QImage.Format_RGBA8888)
                pix = QPixmap.fromImage(qim).scaled(300, 300, Qt.KeepAspectRatio, Qt.SmoothTransformation)"""

content = content.replace(target, replacement)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed segfault bug in payment_dialog.py!")
