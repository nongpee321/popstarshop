import sys
import re

with open(r'D:\pop-erp-food\python-pos\sync_worker.py', 'r', encoding='utf-8') as f:
    content = f.read()

old_payload = '''                payload = {
                    "branch_id": branch_id,
                    "shift_id": shift_id,
                    "cashier_id": cashier_id,
                    "method": receipt.payment_method,
                    "payment_confirmed": True,
                    "cash_received": receipt.received_amount if receipt.payment_method == 'cash' else receipt.total_amount,
                    "cash_amount": receipt.total_amount if receipt.payment_method == 'cash' else 0,
                    "transfer_amount": receipt.total_amount if receipt.payment_method == 'transfer' else 0,
                    "change_amount": max(0, receipt.received_amount - receipt.total_amount) if receipt.payment_method == 'cash' else 0,
                    "items": [
                        {
                            "product_id": item.product_id,
                            "qty": item.qty,
                            "unit_price": item.unit_price
                        } for item in items
                    ]
                }'''

new_payload = '''                payload = {
                    "branch_id": branch_id,
                    "shift_id": shift_id,
                    "cashier_id": cashier_id,
                    "method": receipt.payment_method,
                    "payment_confirmed": True,
                    "cash_received": receipt.received_amount,
                    "cash_amount": getattr(receipt, 'cash_amount', receipt.total_amount if receipt.payment_method == 'cash' else 0),
                    "transfer_amount": getattr(receipt, 'transfer_amount', receipt.total_amount if receipt.payment_method == 'transfer' else 0),
                    "change_amount": max(0, receipt.received_amount - getattr(receipt, 'cash_amount', receipt.total_amount if receipt.payment_method == 'cash' else 0)) if receipt.payment_method in ('cash', 'split') else 0,
                    "is_full_tax": getattr(receipt, 'is_full_tax', False),
                    "customer_name": getattr(receipt, 'customer_name', None),
                    "customer_tax_id": getattr(receipt, 'customer_tax_id', None),
                    "customer_address": getattr(receipt, 'customer_address', None),
                    "items": [
                        {
                            "product_id": item.product_id,
                            "qty": item.qty,
                            "unit_price": item.unit_price
                        } for item in items
                    ]
                }'''

content = content.replace(old_payload, new_payload)

with open(r'D:\pop-erp-food\python-pos\sync_worker.py', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated sync_worker.py")
