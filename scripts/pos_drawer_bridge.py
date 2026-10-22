"""
POS Cash Drawer Bridge for Windows
Listens on http://127.0.0.1:18181/kick to send raw ESC/POS pulse to Xprinter
without opening any browser print dialog or wasting paper.
"""

import sys
import ctypes
from ctypes import wintypes
from http.server import HTTPServer, BaseHTTPRequestHandler
import json

class DOC_INFO_1(ctypes.Structure):
    _fields_ = [
        ('pDocName', wintypes.LPCWSTR),
        ('pOutputFile', wintypes.LPCWSTR),
        ('pDatatype', wintypes.LPCWSTR)
    ]

def kick_cash_drawer():
    winspool = ctypes.WinDLL('winspool.drv')
    hPrinter = wintypes.HANDLE()
    printers = ['XP-80C (copy 2)', 'XP-80C (copy 1)', 'XP-80C']
    success = False
    
    # Standard ESC/POS pulse commands (Pin 2 and Pin 5)
    kick_cmd = bytes([0x1B, 0x70, 0x00, 0x19, 0xFA, 0x1B, 0x70, 0x01, 0x19, 0xFA])
    
    for printer_name in printers:
        if winspool.OpenPrinterW(printer_name, ctypes.byref(hPrinter), None):
            doc_info = DOC_INFO_1('POS Drawer Kick', None, 'RAW')
            job_id = winspool.StartDocPrinterW(hPrinter, 1, ctypes.byref(doc_info))
            if job_id > 0:
                winspool.StartPagePrinter(hPrinter)
                bytes_written = wintypes.DWORD()
                winspool.WritePrinter(hPrinter, kick_cmd, len(kick_cmd), ctypes.byref(bytes_written))
                winspool.EndPagePrinter(hPrinter)
                winspool.EndDocPrinter(hPrinter)
                success = True
            winspool.ClosePrinter(hPrinter)
            if success:
                break
    return success

class BridgeHandler(BaseHTTPRequestHandler):
    def _set_headers(self, status=200):
        self.send_response(status)
        self.send_header('Content-Type', 'application/json; charset=utf-8')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', '*')
        self.end_headers()

    def do_OPTIONS(self):
        self._set_headers(204)

    def do_GET(self):
        if self.path == '/kick':
            ok = kick_cash_drawer()
            self._set_headers(200 if ok else 500)
            self.wfile.write(json.dumps({'ok': ok, 'message': 'Drawer kicked' if ok else 'Printer not found'}).encode('utf-8'))
        elif self.path == '/status':
            self._set_headers(200)
            self.wfile.write(json.dumps({'ok': True, 'status': 'running'}).encode('utf-8'))
        else:
            self._set_headers(404)
            self.wfile.write(json.dumps({'error': 'Not found'}).encode('utf-8'))

    def log_message(self, format, *args):
        pass  # silent logging

def run(port=18181):
    server = HTTPServer(('127.0.0.1', port), BridgeHandler)
    print(f'POS Drawer Bridge running on http://127.0.0.1:{port}')
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        server.server_close()

if __name__ == '__main__':
    run()
