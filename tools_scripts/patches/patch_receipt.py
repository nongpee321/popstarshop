import os

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the title block from HTML
target_html = """            <hr style="border: none; border-top: 1px dashed black; margin: 4px 0;">
            <div style="text-align: center; font-weight: bold; margin-bottom: 8px;">
                ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ
            </div>
            <hr style="border: none; border-top: 1px dashed black; margin: 4px 0;">"""

import re
# Regex to be safe with encoding
content = re.sub(r'<hr style="border: none; border-top: 1px dashed black; margin: 4px 0;">\s*<div style="text-align: center; font-weight: bold; margin-bottom: 8px;">\s*ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ\s*</div>\s*<hr style="border: none; border-top: 1px dashed black; margin: 4px 0;">', '', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Removed HTML title block!")
