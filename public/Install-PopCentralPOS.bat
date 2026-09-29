@echo off
echo ===================================================
echo PopCentral POS - Auto Installer
echo ===================================================
echo.
echo Downloading and installing PopCentral POS...
echo Please wait (this may take 1-2 minutes)...

set INSTALL_DIR=C:\PopCentralPOS
set ZIP_URL=http://localhost:8000/PopCentral-POS-Python.zip
set ZIP_FILE=%TEMP%\PopCentralPOS.zip

if exist "%INSTALL_DIR%" rmdir /s /q "%INSTALL_DIR%"
mkdir "%INSTALL_DIR%"

powershell -Command "Invoke-WebRequest -Uri '%ZIP_URL%' -OutFile '%ZIP_FILE%'"
powershell -Command "Expand-Archive -Path '%ZIP_FILE%' -DestinationPath '%INSTALL_DIR%' -Force"

echo.
echo Creating Desktop Shortcut...
set SHORTCUT_SCRIPT=%TEMP%\CreateShortcut.vbs
echo Set oWS = WScript.CreateObject("WScript.Shell") > "%SHORTCUT_SCRIPT%"
echo sLinkFile = "%USERPROFILE%\Desktop\PopCentral POS.lnk" >> "%SHORTCUT_SCRIPT%"
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> "%SHORTCUT_SCRIPT%"
echo oLink.TargetPath = "%INSTALL_DIR%\PopCentralPOS.exe" >> "%SHORTCUT_SCRIPT%"
echo oLink.WorkingDirectory = "%INSTALL_DIR%" >> "%SHORTCUT_SCRIPT%"
echo oLink.Save >> "%SHORTCUT_SCRIPT%"
cscript /nologo "%SHORTCUT_SCRIPT%"

echo.
echo Installation Completed Successfully!
echo A shortcut "PopCentral POS" has been created on your Desktop.
echo You can double-click it to start the POS.
echo.
pause
