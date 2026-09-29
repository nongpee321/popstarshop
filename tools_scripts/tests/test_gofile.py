import json
import urllib.request
import urllib.parse
import os

req = urllib.request.Request('https://api.gofile.io/servers')
with urllib.request.urlopen(req) as response:
    data = json.loads(response.read().decode())
    server = data['data']['servers'][0]['name']

print(f'Using server: {server}')
