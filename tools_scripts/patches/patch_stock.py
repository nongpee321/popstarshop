import os

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target1 = """                    if stock_qty is not None and (current_cart_qty + 1) > stock_qty:
                        QMessageBox.warning(self, "สินค้าหมด", f"สินค้า '{product.name_th}' หมด!\nมีเหลือแค่ {stock_qty} ชิ้น")
                        return"""
replacement1 = """                    # Removed stock check block
                    # if stock_qty is not None and (current_cart_qty + 1) > stock_qty:
                    #     QMessageBox.warning(self, "สินค้าหมด", f"สินค้า '{product.name_th}' หมด!\\nมีเหลือแค่ {stock_qty} ชิ้น")
                    #     return"""

target2 = """            if stock_qty is not None and 1 > stock_qty:
                QMessageBox.warning(self, "สินค้าหมด", f"สินค้า '{product.name_th}' หมด!\nมีเหลือแค่ {stock_qty} ชิ้น")
                return"""
replacement2 = """            # Removed stock check block
            # if stock_qty is not None and 1 > stock_qty:
            #     QMessageBox.warning(self, "สินค้าหมด", f"สินค้า '{product.name_th}' หมด!\\nมีเหลือแค่ {stock_qty} ชิ้น")
            #     return"""

# Try to find target using regex since Thai string encoding in powershell might be tricky
import re
content = re.sub(r'if stock_qty is not None and \(current_cart_qty \+ 1\) > stock_qty:\s+QMessageBox\.warning[^\n]+\s+return', '# Stock check removed', content)
content = re.sub(r'if stock_qty is not None and 1 > stock_qty:\s+QMessageBox\.warning[^\n]+\s+return', '# Stock check removed', content)

# Bump version to 1.10.61
content = content.replace('v1.10.60', 'v1.10.61')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched main_window.py to remove stock check!")
