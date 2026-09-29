with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

import re
# Add size policy to menu_btn
content = re.sub(
    r'(        menu_btn = QPushButton\("☰ เมนู"\)\n)',
    r'\1        from PySide6.QtWidgets import QSizePolicy\n        menu_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)\n',
    content
)

# Also ensure fs_btn has it
content = re.sub(
    r'(        self\.fs_btn = QPushButton\("🔲 เต็มจอ"\)\n)',
    r'\1        from PySide6.QtWidgets import QSizePolicy\n        self.fs_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)\n',
    content
)

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)
