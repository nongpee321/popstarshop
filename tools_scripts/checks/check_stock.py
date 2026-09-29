import sqlite3
import os

db_path = os.path.join(os.path.expanduser('~'), 'AppData', 'Local', 'PopCentralPOS', 'pos_offline.db')
conn = sqlite3.connect(db_path)
cursor = conn.cursor()
cursor.execute("SELECT id, stock_qty FROM products LIMIT 10")
for row in cursor.fetchall():
    print(row)
conn.close()
