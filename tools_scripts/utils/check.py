import sys
with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()
if "ปิดกะการขาย (ออฟไลน์)" in content:
    print("YES, OFFLINE SHIFT ADDED")
else:
    print("NO, OFFLINE SHIFT NOT ADDED")
