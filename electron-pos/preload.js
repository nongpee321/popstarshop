const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('posApp', {
    getConfigUrl: () => ipcRenderer.invoke('get-config-url'),
    saveConfigUrl: (url) => ipcRenderer.invoke('save-config-url', url),
    retryConnection: () => ipcRenderer.invoke('retry-connection')
});
