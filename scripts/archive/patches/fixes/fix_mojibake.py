import os

file_path = r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'

with open(file_path, 'rb') as f:
    raw_bytes = f.read()

# The file contains UTF-8 bytes that were interpreted as Windows-1252 and saved as UTF-8.
# This means the UTF-8 representation is a double-encoding.
# To fix: decode the file as utf-8, encode as cp1252, then decode as utf-8 again.

text = raw_bytes.decode('utf-8')
try:
    fixed_text = text.encode('cp1252').decode('utf-8')
    with open(file_path, 'wb') as f:
        f.write(fixed_text.encode('utf-8'))
    print("Fixed via cp1252")
except Exception as e:
    print("cp1252 failed:", e)
    try:
        fixed_text = text.encode('latin1').decode('utf-8')
        with open(file_path, 'wb') as f:
            f.write(fixed_text.encode('utf-8'))
        print("Fixed via latin1")
    except Exception as e2:
        print("latin1 failed:", e2)

