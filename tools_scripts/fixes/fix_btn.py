import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

bad_code = '''        layout.addWidget(self.cfd_btn)
        layout.addSpacing(10)
        layout.addWidget(menu_btn)'''

good_code = '''        self.cfd_btn = QPushButton("จอฝั่งลูกค้า")
        self.cfd_btn.setStyleSheet("QPushButton { background-color: #8b5cf6; border: none; color: white; padding: 5px 15px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #7c3aed; }")
        self.cfd_btn.clicked.connect(self.toggle_customer_display)
        
        layout.addWidget(self.cfd_btn)
        layout.addSpacing(10)
        layout.addWidget(menu_btn)'''

content = content.replace(bad_code, good_code)
content = content.replace('v1.10.51', 'v1.10.52')

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)
