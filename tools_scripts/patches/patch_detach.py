import os

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """            session.add(new_receipt)
            session.add_all(items)
            session.commit()
            session.close()"""

replacement = """            session.add(new_receipt)
            session.add_all(items)
            
            # Extract data before commit expires the instances
            receipt_items_data = [{'name': i.name, 'qty': i.qty, 'price': i.unit_price, 'total': i.total_price} for i in items]
            
            session.commit()
            session.close()"""

if target in content:
    content = content.replace(target, replacement)
    
    target2 = "'items': [{'name': i.name, 'qty': i.qty, 'price': i.unit_price, 'total': i.total_price} for i in items]"
    replacement2 = "'items': receipt_items_data"
    
    if target2 in content:
        content = content.replace(target2, replacement2)
        # Bump version to 1.10.60
        content = content.replace("v1.10.59", "v1.10.60")
        with open(path, 'w', encoding='utf-8') as f:
            f.write(content)
        print("Patched main_window.py for detached instance error successfully!")
    else:
        print("Could not find target2")
else:
    print("Could not find target1")
