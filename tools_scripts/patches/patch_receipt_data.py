import os
import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """            self.update_cart_total()
            
            # Show Receipt Dialog"""

replacement = """            self.update_cart_total()
            
            # Show Receipt Dialog
            from datetime import datetime
            receipt_data = {
                'doc_number': doc_no,
                'total': total_amount,
                'payment_method': method_map.get(method, 'cash'),
                'is_full_tax': is_full_tax,
                'customer_name': cust_name,
                'customer_tax_id': cust_tax,
                'customer_address': cust_addr,
                'cashier': self.config.get('cashier_name', 'Unknown'),
                'branch': self.config.get('branch_name', ''),
                'date': datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                'items': [{'name': i.name, 'qty': i.qty, 'price': i.unit_price, 'total': i.total_price} for i in items]
            }"""

if target in content:
    content = content.replace(target, replacement)
    # let's also bump version to 1.10.59
    content = content.replace("v1.10.58", "v1.10.59")
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Patched main_window.py successfully!")
else:
    print("Could not find the target string!")
