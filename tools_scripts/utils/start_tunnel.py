import subprocess
import time

while True:
    print('Starting tunnel...')
    subprocess.run(['C:\\Program Files (x86)\\cloudflared\\cloudflared.exe', 'tunnel', '--url', 'http://localhost:8000'])
    time.sleep(3)
