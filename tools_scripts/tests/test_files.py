import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\storage\app\pos-python-releases'
files = os.listdir(path)

for f in files:
    m = re.search(r'-(\d+\.\d+\.\d+)-setup\.exe$', f)
    if m:
        print(f"{f} -> {m.group(1)}")
