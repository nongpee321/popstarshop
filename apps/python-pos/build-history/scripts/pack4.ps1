cd d:\pop-erp-food\ERPPOP-main\apps\python-pos
cd dist
& "C:\Program Files\7-Zip\7z.exe" a -sfx"C:\Program Files\7-Zip\7z.sfx" PopCentral-POS-UAT-1.11.35-setup.exe "PopCentral POS\*"
Move-Item -Force PopCentral-POS-UAT-1.11.35-setup.exe d:\pop-erp-food\ERPPOP-main\storage\app\pos-python-releases\
