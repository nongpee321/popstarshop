import sys
import os
import traceback

def main():
    try:
        from PySide6.QtWidgets import QApplication, QMessageBox
        from ui.main_window import MainWindow
        from database.models import init_db
        
        # 1. Initialize SQLite Database (Offline Storage)
        app_data_dir = os.path.join(os.path.expanduser('~'), 'AppData', 'Local', 'PopCentralPOS')
        os.makedirs(app_data_dir, exist_ok=True)
        db_path = os.path.join(app_data_dir, 'pos_offline.db')
        
        session = init_db(f'sqlite:///{db_path}')
        
        # 2. Start Application
        app = QApplication(sys.argv)
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
        
        # Try to show popup if Qt loaded
        try:
            from PySide6.QtWidgets import QApplication, QMessageBox
            if not QApplication.instance():
                app = QApplication(sys.argv)
            QMessageBox.critical(None, "Fatal Error", f"Application failed to start:\n{error_msg}")
        except:
            pass
        input("\n[ERROR] Program crashed! Press ENTER to exit...")
        sys.exit(1)

if __name__ == "__main__":
    main()
