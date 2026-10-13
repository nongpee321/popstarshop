cd d:\pop-erp-food\ERPPOP-main\apps\python-pos
.\venv\Scripts\activate
pyinstaller --noconfirm --onedir --windowed --icon=assets\icon.ico --name "PopCentral POS" --add-data "assets;assets" main.py
cd dist
& "C:\Program Files\7-Zip\7z.exe" a -sfx"C:\Program Files\7-Zip\7z.sfx" PopCentral-POS-UAT-1.11.34-setup.exe "PopCentral POS\*"
Move-Item -Force PopCentral-POS-UAT-1.11.34-setup.exe d:\pop-erp-food\ERPPOP-main\storage\app\pos-python-releases\
