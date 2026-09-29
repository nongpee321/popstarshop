with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

import re
content = re.sub(r"self\.cashier_btn\.setToolTip\('.*?\)'.*?\n.*?setSizePolicy.*?Fixed\)", "self.cashier_btn.setToolTip('แคชเชียร์')\n        from PySide6.QtWidgets import QSizePolicy\n        self.cashier_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)", content, flags=re.DOTALL)

content = re.sub(r"self\.shift_btn\.setToolTip\('.*?\)'.*?\n.*?setSizePolicy.*?Fixed\)", "self.shift_btn.setToolTip('จัดการกะ (Shift)')\n        from PySide6.QtWidgets import QSizePolicy\n        self.shift_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)", content, flags=re.DOTALL)

content = re.sub(r"self\.cfd_btn\.setToolTip\('.*?\)'.*?\n.*?setSizePolicy.*?Fixed\)", "self.cfd_btn.setToolTip('จอฝั่งลูกค้า')\n        from PySide6.QtWidgets import QSizePolicy\n        self.cfd_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)", content, flags=re.DOTALL)

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)
