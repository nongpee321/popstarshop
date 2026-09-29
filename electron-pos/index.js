const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');

let mainWindow;

function getConfigPath() {
    return app.isPackaged
        ? path.join(path.dirname(process.execPath), 'config.txt')
        : path.join(__dirname, 'config.txt');
}

function getSavedUrl() {
    const configPath = getConfigPath();
    if (fs.existsSync(configPath)) {
        try {
            const fileContent = fs.readFileSync(configPath, 'utf8').trim();
            if (fileContent) {
                return fileContent.startsWith('http') ? fileContent : 'http://' + fileContent;
            }
        } catch (e) {}
    }
    // Default fallback to local network server or localhost
    return 'http://127.0.0.1:8000/pos';
}

function saveUrl(newUrl) {
    const configPath = getConfigPath();
    let formatted = String(newUrl || '').trim();
    if (formatted && !formatted.startsWith('http')) {
        formatted = 'http://' + formatted;
    }
    if (formatted && !formatted.includes('/pos')) {
        formatted = formatted.replace(/\/+$/, '') + '/pos';
    }
    try {
        fs.writeFileSync(configPath, formatted, 'utf8');
        return true;
    } catch (e) {
        return false;
    }
}

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        minWidth: 1024,
        minHeight: 768,
        autoHideMenuBar: true,
        icon: path.join(__dirname, 'icon.png'),
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    mainWindow.maximize();
    loadPosPage();
}

function loadPosPage() {
    const targetUrl = getSavedUrl();
    mainWindow.loadURL(targetUrl).catch((err) => {
        console.error('Failed to load POS page:', err);
        mainWindow.loadFile(path.join(__dirname, 'error.html'), {
            query: { currentUrl: targetUrl }
        });
    });
}

// IPC Handlers for error page configuration
ipcMain.handle('get-config-url', () => {
    return getSavedUrl();
});

ipcMain.handle('save-config-url', (event, newUrl) => {
    const success = saveUrl(newUrl);
    if (success) {
        loadPosPage();
    }
    return success;
});

ipcMain.handle('retry-connection', () => {
    loadPosPage();
    return true;
});

app.whenReady().then(createWindow);

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') app.quit();
});
