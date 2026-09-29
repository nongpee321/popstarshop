import os

path = r'D:\pop-erp-food\python-pos\ui\customer_display.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix QR Generation
qr_gen_target = """        # Convert PIL image to QPixmap
        qr_img = qr_img.convert("RGBA")
        data = qr_img.tobytes("raw", "RGBA")
        qim = QImage(data, qr_img.size[0], qr_img.size[1], QImage.Format_RGBA8888)
        pixmap = QPixmap.fromImage(qim)
        return pixmap.scaled(450, 450, Qt.KeepAspectRatio, Qt.SmoothTransformation)"""

qr_gen_replace = """        import io
        buf = io.BytesIO()
        qr_img.save(buf, format="PNG")
        pixmap = QPixmap()
        pixmap.loadFromData(buf.getvalue())
        return pixmap.scaled(450, 450, Qt.KeepAspectRatio, Qt.SmoothTransformation)"""
content = content.replace(qr_gen_target, qr_gen_replace)

# Fix Font Sizes
content = content.replace("font-size: 26px;", "font-size: 18px;")
content = content.replace("font-size: 24px;", "font-size: 18px;")
content = content.replace("font-size: 90px;", "font-size: 70px;")
content = content.replace("font-size: 40px;", "font-size: 32px;")
content = content.replace("font-size: 48px;", "font-size: 36px;")
content = content.replace("QFont(\"Segoe UI\", 36, QFont.Bold)", "QFont(\"Segoe UI\", 24, QFont.Bold)")
content = content.replace("self.table.setRowHeight(i, 80)", "self.table.setRowHeight(i, 50)")
content = content.replace("padding: 15px;", "padding: 10px;")
content = content.replace("padding: 20px 40px;", "padding: 10px 20px;")
content = content.replace("self.table.setHorizontalHeaderLabels([\"รายการสินค้า\", \"จำนวน\", \"ราคา\", \"รวม\"])", "self.table.setHorizontalHeaderLabels(['สินค้า', 'จำนวน', 'ราคา', 'รวม'])")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched customer_display.py!")
