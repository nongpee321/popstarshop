from sqlalchemy import create_engine, Column, Integer, String, Float, Boolean, DateTime, ForeignKey, Text, event, text
from sqlalchemy.orm import declarative_base, relationship, sessionmaker
from datetime import datetime
import os
import shutil
import uuid

Base = declarative_base()

# 1. app_settings
class AppSetting(Base):
    __tablename__ = 'app_settings'
    
    key = Column(String(100), primary_key=True)
    value = Column(Text, nullable=True)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

# 2. device_config
class DeviceConfig(Base):
    __tablename__ = 'device_config'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    device_id = Column(String(100), nullable=False)
    branch_id = Column(Integer, nullable=True)
    branch_name = Column(String(255), nullable=True)
    device_token = Column(Text, nullable=True)
    api_url = Column(String(255), default='http://localhost:8000')
    registered_at = Column(DateTime, default=datetime.utcnow)
    is_active = Column(Boolean, default=True)

# 3. products
class Product(Base):
    __tablename__ = 'products'
    
    id = Column(Integer, primary_key=True)
    category_id = Column(Integer, nullable=True)
    sku_code = Column(String(50), unique=True, nullable=False)
    barcode = Column(String(50), nullable=True)
    name_th = Column(String(255), nullable=False)
    name_en = Column(String(255), nullable=True)
    default_price = Column(Float, nullable=False, default=0.0)
    pos_price = Column(Float, nullable=True)
    is_active = Column(Boolean, default=True)
    stock_qty = Column(Float, nullable=True)
    image_url = Column(String(255), nullable=True)
    is_scale_item = Column(Boolean, default=False)
    scale_type = Column(String(20), default='weight') # weight (kg), piece

# 4. scale_profiles
class ScaleProfile(Base):
    __tablename__ = 'scale_profiles'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    prefix = Column(String(10), nullable=False) # e.g. '800', '801', '02', '20'
    item_code_len = Column(Integer, default=5)
    weight_len = Column(Integer, default=5)
    weight_divisor = Column(Float, default=1000.0) # 1000 -> grams to kg
    price_len = Column(Integer, default=0)
    price_divisor = Column(Float, default=100.0)
    is_active = Column(Boolean, default=True)

# 5. cashiers
class Cashier(Base):
    __tablename__ = 'cashiers'
    
    id = Column(Integer, primary_key=True)
    username = Column(String(100), unique=True, nullable=False)
    name = Column(String(255), nullable=False)
    pin_hash = Column(String(255), nullable=True)
    role = Column(String(50), default='cashier')
    is_active = Column(Boolean, default=True)

# 6. shifts
class PosShift(Base):
    __tablename__ = 'shifts'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    server_id = Column(Integer, nullable=True)
    branch_id = Column(Integer, nullable=True)
    cashier_id = Column(Integer, nullable=True)
    cashier_name = Column(String(255), nullable=True)
    opened_at = Column(DateTime, default=datetime.utcnow)
    closed_at = Column(DateTime, nullable=True)
    opening_cash = Column(Float, default=0.0)
    expected_cash = Column(Float, nullable=True)
    counted_cash = Column(Float, nullable=True)
    status = Column(String(20), default='open') # open, closed
    sync_status = Column(String(20), default='pending') # pending, synced

# 7. sales
class Sale(Base):
    __tablename__ = 'sales'
    
    local_sale_id = Column(String(50), primary_key=True) # UUID
    server_receipt_id = Column(String(100), nullable=True)
    device_id = Column(String(100), nullable=True)
    branch_id = Column(Integer, nullable=True)
    shift_id = Column(Integer, nullable=True)
    cashier_salesman_id = Column(Integer, nullable=True)
    total_amount = Column(Float, nullable=False, default=0.0)
    received_amount = Column(Float, nullable=False, default=0.0)
    status = Column(String(20), nullable=False, default='pending') # pending, synced, failed
    idempotency_key = Column(String(100), unique=True, nullable=False)
    created_at = Column(DateTime, default=datetime.utcnow)
    synced_at = Column(DateTime, nullable=True)
    
    # Metadata for offline receipt compatibility
    doc_number = Column(String(50), nullable=True)
    payment_method = Column(String(20), nullable=False, default='cash')
    cash_amount = Column(Float, nullable=True, default=0.0)
    transfer_amount = Column(Float, nullable=True, default=0.0)
    is_full_tax = Column(Boolean, default=False)
    customer_name = Column(String(255), nullable=True)
    customer_tax_id = Column(String(50), nullable=True)
    customer_address = Column(Text, nullable=True)
    sync_error = Column(Text, nullable=True)
    
    items = relationship('SaleItem', backref='sale', cascade="all, delete-orphan")
    payments = relationship('Payment', backref='sale', cascade="all, delete-orphan")

# Compatibility Alias for PosReceipt
PosReceipt = Sale

# 8. sale_items
class SaleItem(Base):
    __tablename__ = 'sale_items'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    sale_id = Column(String(50), ForeignKey('sales.local_sale_id'), nullable=False)
    product_id = Column(Integer, nullable=False)
    sku_code = Column(String(50), nullable=False)
    barcode = Column(String(50), nullable=True)
    name = Column(String(255), nullable=False)
    qty = Column(Float, nullable=False, default=1.0)
    unit_price = Column(Float, nullable=False)
    total_price = Column(Float, nullable=False)
    is_weight_item = Column(Boolean, default=False)

# Compatibility Alias for PosReceiptItem
PosReceiptItem = SaleItem
# Backward compatibility attribute alias
SaleItem.receipt_id = SaleItem.sale_id

# 9. payments
class Payment(Base):
    __tablename__ = 'payments'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    sale_id = Column(String(50), ForeignKey('sales.local_sale_id'), nullable=False)
    method = Column(String(20), nullable=False) # cash, transfer, card
    amount = Column(Float, nullable=False, default=0.0)
    received_amount = Column(Float, nullable=True, default=0.0)
    change_amount = Column(Float, nullable=True, default=0.0)
    reference_no = Column(String(100), nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)

# 10. outbox
class Outbox(Base):
    __tablename__ = 'outbox'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    event_type = Column(String(50), nullable=False) # 'sale_checkout', 'shift_close', etc.
    aggregate_id = Column(String(50), nullable=False) # local_sale_id
    payload_json = Column(Text, nullable=False)
    idempotency_key = Column(String(100), unique=True, nullable=False)
    status = Column(String(20), default='pending') # pending, processing, synced, failed
    retry_count = Column(Integer, default=0)
    max_retries = Column(Integer, default=10)
    last_error = Column(Text, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    synced_at = Column(DateTime, nullable=True)

# 11. sync_logs
class SyncLog(Base):
    __tablename__ = 'sync_logs'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    direction = Column(String(20), default='push') # push, pull
    sync_type = Column(String(50), nullable=False) # products, sales, shifts
    record_count = Column(Integer, default=0)
    status = Column(String(20), nullable=False) # success, failed
    message = Column(Text, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)

# Existing helper classes
class Category(Base):
    __tablename__ = 'categories'
    
    id = Column(Integer, primary_key=True)
    name_th = Column(String(100), nullable=False)
    is_active = Column(Boolean, default=True)

class CancelledBill(Base):
    __tablename__ = 'cancelled_bills'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    shift_id = Column(Integer, nullable=True)
    reason = Column(Text, nullable=False)
    total_amount = Column(Float, nullable=False, default=0.0)
    items_json = Column(Text, nullable=False)
    created_at = Column(DateTime, default=datetime.utcnow)

# Helper paths
def get_pos_data_paths():
    local_app_data = os.environ.get('LOCALAPPDATA') or os.path.join(os.path.expanduser('~'), 'AppData', 'Local')
    base_dir = os.path.join(local_app_data, 'PopCentralPOS')
    data_dir = os.path.join(base_dir, 'data')
    backup_dir = os.path.join(base_dir, 'backups')
    
    os.makedirs(data_dir, exist_ok=True)
    os.makedirs(backup_dir, exist_ok=True)
    
    db_file = os.path.join(data_dir, 'pos.db')
    return {
        'base_dir': base_dir,
        'data_dir': data_dir,
        'backup_dir': backup_dir,
        'db_file': db_file
    }

def backup_pos_db(source_db_file=None, comment="auto"):
    paths = get_pos_data_paths()
    src = source_db_file or paths['db_file']
    if not os.path.exists(src):
        return None
        
    today_str = datetime.now().strftime('%Y%m%d_%H%M%S')
    backup_name = f"pos_backup_{today_str}_{comment}.db"
    dest = os.path.join(paths['backup_dir'], backup_name)
    
    try:
        shutil.copy2(src, dest)
        # Keep only the last 30 backups to save space
        backups = sorted([
            os.path.join(paths['backup_dir'], f)
            for f in os.listdir(paths['backup_dir'])
            if f.startswith('pos_backup_') and f.endswith('.db')
        ], key=os.path.getmtime)
        while len(backups) > 30:
            oldest = backups.pop(0)
            try:
                os.remove(oldest)
            except Exception:
                pass
        return dest
    except Exception as e:
        print(f"Error creating backup: {e}")
        return None

def restore_pos_db(backup_file_path):
    paths = get_pos_data_paths()
    dest = paths['db_file']
    if not os.path.exists(backup_file_path):
        raise FileNotFoundError(f"Backup file not found: {backup_file_path}")
        
    # Backup current before overwriting
    if os.path.exists(dest):
        backup_pos_db(dest, comment="pre_restore")
        
    shutil.copy2(backup_file_path, dest)
    return True

def init_db(db_path=None):
    if not db_path:
        paths = get_pos_data_paths()
        db_path = f"sqlite:///{paths['db_file']}"
        
    engine = create_engine(db_path, connect_args={'check_same_thread': False})
    
    # Enable WAL mode and foreign keys for SQLite
    @event.listens_for(engine, "connect")
    def set_sqlite_pragma(dbapi_connection, connection_record):
        cursor = dbapi_connection.cursor()
        cursor.execute("PRAGMA journal_mode=WAL")
        cursor.execute("PRAGMA foreign_keys=ON")
        cursor.close()

    # Create tables if not exist
    Base.metadata.create_all(engine)
    
    # Seed default scale profiles if empty
    Session = sessionmaker(bind=engine)
    session = Session()
    try:
        count = session.query(ScaleProfile).count()
        if count == 0:
            profiles = [
                ScaleProfile(name="Scale 800 (Weight in grams)", prefix="800", item_code_len=4, weight_len=5, weight_divisor=1000.0, is_active=True),
                ScaleProfile(name="Scale 801 (Weight in grams)", prefix="801", item_code_len=4, weight_len=5, weight_divisor=1000.0, is_active=True),
                ScaleProfile(name="Scale 02 (Standard Retail)", prefix="02", item_code_len=5, weight_len=5, weight_divisor=1000.0, is_active=True),
                ScaleProfile(name="Scale 20 (Standard Retail)", prefix="20", item_code_len=5, weight_len=5, weight_divisor=1000.0, is_active=True),
            ]
            session.add_all(profiles)
            session.commit()
    except Exception:
        session.rollback()
        
    return session
