import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Let's see if self.cfd_btn already exists
if 'self.cfd_btn' not in content:
    print('Adding cfd_btn...')
    # Define the button right before menu_btn is created
    create_menu_btn = '        menu_btn = QPushButton("≡ เมนู")'
    create_cfd_btn = '''        self.cfd_btn = QPushButton("จอฝั่งลูกค้า")
        self.cfd_btn.setStyleSheet("QPushButton { background-color: #8b5cf6; border: none; color: white; padding: 5px 15px; border-radius: 6px; font-weight: bold;} QPushButton:hover { background-color: #7c3aed; }")
        self.cfd_btn.clicked.connect(self.toggle_customer_display)
        
        menu_btn = QPushButton("≡ เมนู")'''
    content = content.replace(create_menu_btn, create_cfd_btn)
    
    # Add it to the layout before menu_btn
    add_menu_btn = '        layout.addWidget(menu_btn)'
    add_cfd_btn = '''        layout.addWidget(self.cfd_btn)
        layout.addSpacing(10)
        layout.addWidget(menu_btn)'''
    content = content.replace(add_menu_btn, add_cfd_btn)

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done modifying layout in main_window.py')
