import os
import re

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the print logic block for the title
content = re.sub(
    r'cmds \+= LINE_FEED\s+if is_full_tax:\s+cmds \+= DOUBLE_ON \+ BOLD_ON \+ enc\("ใบกำกับภาษีเต็มรูป"\) \+ DOUBLE_OFF \+ BOLD_OFF \+ LINE_FEED\s+cmds \+= DOUBLE_ON \+ BOLD_ON \+ enc\("ใบเสร็จรับเงิน"\) \+ DOUBLE_OFF \+ BOLD_OFF \+ LINE_FEED\s+else:\s+cmds \+= DOUBLE_ON \+ BOLD_ON \+ enc\("ใบเสร็จรับเงินอย่างย่อ"\) \+ DOUBLE_OFF \+ BOLD_OFF \+ LINE_FEED\s+cmds \+= LINE_FEED',
    '',
    content
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Removed title from ESC/POS print logic!")
