import os

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

drawer_func = '''
def open_cash_drawer(printer_name):
    try:
        import win32print
    except ImportError:
        raise Exception("Cannot import win32print")
        
    if not printer_name:
        raise Exception("No printer configured")
        
    ESC = b'\x1b'
    INIT = ESC + b'@'
    OPEN_DRAWER_1 = ESC + b'p\x00\x19\xfa'
    OPEN_DRAWER_2 = ESC + b'p\x01\x19\xfa'
    
    cmds = bytearray()
    cmds += INIT
    cmds += OPEN_DRAWER_1
    cmds += OPEN_DRAWER_2
    
    try:
        hPrinter = win32print.OpenPrinter(printer_name)
        try:
            win32print.StartDocPrinter(hPrinter, 1, ("OpenDrawer", None, "RAW"))
            win32print.StartPagePrinter(hPrinter)
            win32print.WritePrinter(hPrinter, bytes(cmds))
            win32print.EndPagePrinter(hPrinter)
            win32print.EndDocPrinter(hPrinter)
        finally:
            win32print.ClosePrinter(hPrinter)
    except Exception as e:
        raise Exception(f"Failed to open drawer: {str(e)}")

'''

# Inject drawer_func before class ReceiptSettingsDialog
content = content.replace('class ReceiptSettingsDialog(QDialog):', drawer_func + 'class ReceiptSettingsDialog(QDialog):')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Injected open_cash_drawer")
