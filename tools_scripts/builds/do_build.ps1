cd D:\pop-erp-food\python-pos
.\venv\Scripts\Activate.ps1
pyinstaller --noconfirm --onedir --windowed --icon=assets\icon.ico --name "PopCentral POS" --add-data "assets;assets" main.py
cd dist
& "C:\Program Files\7-Zip\7z.exe" a -sfx PopCentral-POS-UAT-1.10.57-setup.exe "PopCentral POS\*"
Move-Item -Force PopCentral-POS-UAT-1.10.57-setup.exe D:\pop-erp-food\ERPPOP-main\storage\app\pos-python-releases\
