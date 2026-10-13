with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(len(lines)):
    if 'if hasattr(self, "branch_label"): self.branch_label.show()' in lines[i]:
        # Count leading spaces of the PREVIOUS line
        prev_line = lines[i-1]
        spaces = len(prev_line) - len(prev_line.lstrip(' '))
        lines[i] = (' ' * spaces) + 'if hasattr(self, "branch_label"): self.branch_label.show()\n'
    elif 'if hasattr(self, "branch_label"): self.branch_label.hide()' in lines[i]:
        prev_line = lines[i-1]
        spaces = len(prev_line) - len(prev_line.lstrip(' '))
        lines[i] = (' ' * spaces) + 'if hasattr(self, "branch_label"): self.branch_label.hide()\n'

with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
