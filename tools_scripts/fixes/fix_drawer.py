with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'def open_cash_drawer' in line:
        # Search for cmds = bytearray() within this function
        for j in range(i, i+30):
            if 'cmds = bytearray()' in lines[j]:
                # We expect cmds += INIT on the next line
                if 'cmds += INIT' in lines[j+1]:
                    # Insert the drawer kicks right after INIT
                    lines.insert(j+2, "    cmds += OPEN_DRAWER_1\n")
                    lines.insert(j+3, "    cmds += OPEN_DRAWER_2\n")
                    print("Fixed open_cash_drawer")
                    break
        break

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
