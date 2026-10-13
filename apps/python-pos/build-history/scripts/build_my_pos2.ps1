cd d:\pop-erp-food\ERPPOP-main\apps\python-pos
.\venv\Scripts\activate
pyinstaller --noconfirm --onedir --windowed --icon=assets\icon.ico --name "PopCentral POS" --add-data "assets;assets" --hidden-import "ui.main_window" --hidden-import "database.models" --hidden-import "sync_worker" --hidden-import "qrcode" --hidden-import "escpos" --hidden-import "barcode" --hidden-import "PIL" --hidden-import "win32print" --hidden-import "win32ui" --hidden-import "win32con" --hidden-import "ui.customer_display" --hidden-import "ui.login_dialog" --hidden-import "ui.shift_dialog" main.py
cd dist
& "C:\Program Files\7-Zip\7z.exe" a -sfx"C:\Program Files\7-Zip\7z.sfx" PopCentral-POS-UAT-1.11.33-setup.exe "PopCentral POS\*"
Move-Item -Force PopCentral-POS-UAT-1.11.33-setup.exe d:\pop-erp-food\ERPPOP-main\storage\app\pos-python-releases\
