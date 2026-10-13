import re
file_path = r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'
with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

def robust_decode(s):
    # s is utf-8 string containing mojibake
    # We will try to get raw bytes back by treating each char as 1 byte if possible
    # But python already read it as utf-8, so some chars are expanded
    # The actual bug: it was a UTF-8 file, read as CP1252/Latin-1, and saved as UTF-8.
    try:
        return s.encode('latin1').decode('utf-8')
    except:
        return "ERR: " + repr(s)

matches = set(re.findall(r'[\'"]([^\'"]*à[^\'"]*)[\'"]', text))
for m in matches:
    print(repr(m), "=>", robust_decode(m))
