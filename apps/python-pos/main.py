import ui.main_window
import ui.login_dialog
import ui.shift_dialog
import ui.customer_display
import ui.token_dialog
import database.models
import sync_worker
import sys
import os
import json
import traceback

def main():
    try:
        from PySide6.QtWidgets import QApplication, QMessageBox
        from ui.main_window import MainWindow
        from ui.token_dialog import PosTokenLoginDialog
        from database.models import init_db, Product, Category
        from PySide6.QtCore import QLocale

        # 1. Initialize SQLite Database (%LOCALAPPDATA%\PopCentralPOS\data\pos.db)
        from database.models import get_pos_data_paths, backup_pos_db
        paths = get_pos_data_paths()
        app_data_dir = paths['base_dir']
        db_path = paths['db_file']
        
        # Initialize database first so tables exist
        session = init_db(f'sqlite:///{db_path}')

        # Migrate legacy pos_offline.db if exists and pos.db is newly created/empty
        legacy_db = os.path.join(paths['base_dir'], 'pos_offline.db')
        if os.path.exists(legacy_db):
            try:
                import sqlite3
                prod_count = session.query(Product).count()
                if prod_count == 0:
                    old_conn = sqlite3.connect(legacy_db)
                    old_cur = old_conn.cursor()
                    
                    # Copy categories
                    old_cur.execute('SELECT id, name_th, is_active FROM categories')
                    for r in old_cur.fetchall():
                        session.merge(Category(id=r[0], name_th=r[1], is_active=bool(r[2])))
                        
                    # Copy products
                    old_cur.execute('SELECT id, category_id, sku_code, barcode, name_th, name_en, default_price, pos_price, is_active, stock_qty, image_url FROM products')
                    for r in old_cur.fetchall():
                        session.merge(Product(
                            id=r[0], category_id=r[1], sku_code=r[2], barcode=r[3],
                            name_th=r[4], name_en=r[5], default_price=r[6], pos_price=r[7],
                            is_active=bool(r[8]), stock_qty=r[9], image_url=r[10]
                        ))
                    session.commit()
                    old_conn.close()
            except Exception:
                session.rollback()

        # Perform daily automatic backup on startup
        try:
            backup_pos_db(db_path, comment="daily_startup")
        except Exception:
            pass

        # 2. Start Application
        app = QApplication(sys.argv)
        QLocale.setDefault(QLocale(QLocale.English, QLocale.UnitedStates))

        # 3. Load config and prompt Token Login FIRST before showing MainWindow
        config_file = os.path.join(app_data_dir, 'config.json')
        config = {}
        if os.path.exists(config_file):
            try:
                with open(config_file, 'r', encoding='utf-8') as f:
                    config = json.load(f)
            except:
                pass

        is_demo = '--demo' in sys.argv
        if is_demo:
            print("[POS] Starting in DEMO mode...")
            config['api_url'] = 'http://localhost:8000'
            config['branch_id'] = 6
            config['branch_name'] = '0001 - สำนักงานใหญ่'
            config['device_id'] = 1
            config['cashier_name'] = 'Admin / Demo Cashier'
            config['cashier_username'] = 'admin'
        else:
            token_dlg = PosTokenLoginDialog(
                api_url=config.get('api_url', 'http://localhost:8000'),
                default_token=config.get('pos_token', ''),
                default_username=config.get('cashier_username', config.get('cashier_name', ''))
            )
            if token_dlg.exec() != PosTokenLoginDialog.Accepted:
                # User closed or cancelled login window -> exit app without opening MainWindow
                sys.exit(0)

        if not is_demo:
            # Update config with verified token, branch, and cashier identity
            config['pos_token'] = token_dlg.token
            if getattr(token_dlg, 'branch_id', None):
                config['branch_id'] = token_dlg.branch_id
            if getattr(token_dlg, 'branch_name', None):
                config['branch_name'] = token_dlg.branch_name
            if getattr(token_dlg, 'device_id', None):
                config['device_id'] = token_dlg.device_id
            if token_dlg.selected_cashier:
                cashier_obj = token_dlg.selected_cashier
                cashier_name = cashier_obj.get('name') or cashier_obj.get('code') or token_dlg.username
                cashier_id = cashier_obj.get('id') or cashier_obj.get('user_id')
                if cashier_id:
                    config['cashier_id'] = cashier_id
                config['cashier_name'] = cashier_name
                config['cashier_username'] = token_dlg.username
                config['cashier_pin'] = token_dlg.entered_pin
        else:
            config['pos_token'] = config.get('pos_token') or 'demo_token'
        try:
            with open(config_file, 'w', encoding='utf-8') as f:
                json.dump(config, f)
        except:
            pass

        # 4. Now create and show MainWindow
        window = MainWindow(db_path=db_path)
        window.show()
        sys.exit(app.exec())
    except Exception as e:
        error_msg = traceback.format_exc()
        try:
            with open(os.path.join(os.path.expanduser('~'), 'Desktop', 'POS_CRASH.txt'), 'w', encoding='utf-8') as f:
                f.write(error_msg)
        except:
            pass
        
        try:
            from PySide6.QtWidgets import QApplication, QMessageBox
            if not QApplication.instance():
                app = QApplication(sys.argv)
            QMessageBox.critical(None, "Fatal Error", f"Application failed to start:\n{error_msg}")
        except:
            pass

if __name__ == '__main__':
    main()
