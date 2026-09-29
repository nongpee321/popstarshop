import sys
with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

import re
match = re.search(r'action_revenue = main_menu\.addAction\(.*?menu_btn\.setMenu\(main_menu\)', content, re.DOTALL)
if match:
    old_menu = match.group(0)
    new_menu = '''action_revenue = main_menu.addAction("ดูรายได้วันนี้")
        action_revenue.triggered.connect(self.show_today_revenue)
        
        main_menu.addSeparator()
        
        action_sync = main_menu.addAction("ซิงค์ข้อมูล (Sync Data)")
        action_sync.triggered.connect(self.manual_sync)
        
        main_menu.addSeparator()
        
        action_settings = main_menu.addAction("ตั้งค่าระบบ")
        action_settings.triggered.connect(self.open_settings)
        
        menu_btn.setMenu(main_menu)'''
        
    content = content.replace(old_menu, new_menu)
    with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Menu replaced successfully!")
else:
    print("Could not find menu code.")
