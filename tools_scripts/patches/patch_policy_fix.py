with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(len(lines)):
    if 'self.shift_btn.setToolTip(' in lines[i] and 'setSizePolicy' in lines[i+1]:
        lines[i] = lines[i].replace('\n', "')\n")
        lines[i+1] = lines[i+1].replace("')", "")

    if 'self.cashier_btn.setToolTip(' in lines[i] and 'setSizePolicy' in lines[i+2]:
        lines[i] = lines[i].replace('\n', "')\n")
        lines[i+2] = lines[i+2].replace("')", "")
        
    if 'self.cfd_btn.setToolTip(' in lines[i] and 'setSizePolicy' in lines[i+1]:
        lines[i] = lines[i].replace('\n', "')\n")
        lines[i+1] = lines[i+1].replace("')", "")

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
