from sqlalchemy import create_engine, Column, Integer, String, Float, Boolean, DateTime, ForeignKey, Text, text
from sqlalchemy.orm import declarative_base, relationship, sessionmaker
from datetime import datetime
import os

Base = declarative_base()

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
    
class Category(Base):
    __tablename__ = 'categories'
    
    id = Column(Integer, primary_key=True)
    name_th = Column(String(100), nullable=False)
    is_active = Column(Boolean, default=True)

class PosShift(Base):
    __tablename__ = 'shifts'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    server_id = Column(Integer, nullable=True)
    opened_at = Column(DateTime, default=datetime.utcnow)
    closed_at = Column(DateTime, nullable=True)
    opening_cash = Column(Float, default=0.0)
    expected_cash = Column(Float, nullable=True)
    counted_cash = Column(Float, nullable=True)
    status = Column(String(20), default='open') # open, closed
    sync_status = Column(String(20), default='pending') # pending, synced
    
class PosReceipt(Base):
    __tablename__ = 'receipts'
    
    id = Column(String(50), primary_key=True) # UUID for offline creation
    shift_id = Column(Integer, nullable=True) # Local shift id
    doc_number = Column(String(50), nullable=True)
    total_amount = Column(Float, nullable=False, default=0.0)
    received_amount = Column(Float, nullable=False, default=0.0)
    payment_method = Column(String(20), nullable=False, default='cash') # cash, transfer, card, split
    cash_amount = Column(Float, nullable=True, default=0.0)
    transfer_amount = Column(Float, nullable=True, default=0.0)
    is_full_tax = Column(Boolean, default=False)
    customer_name = Column(String(255), nullable=True)
    customer_tax_id = Column(String(50), nullable=True)
    customer_address = Column(Text, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    sync_status = Column(String(20), default='pending') # pending, synced, error
    sync_error = Column(Text, nullable=True)
    
class PosReceiptItem(Base):
    __tablename__ = 'receipt_items'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    receipt_id = Column(String(50), ForeignKey('receipts.id'), nullable=False)
    product_id = Column(Integer, nullable=False)
    sku_code = Column(String(50), nullable=False)
    name = Column(String(255), nullable=False)
    qty = Column(Float, nullable=False, default=1.0)
    unit_price = Column(Float, nullable=False)
    total_price = Column(Float, nullable=False)
    
    receipt = relationship('PosReceipt', backref='items')


class CancelledBill(Base):
    __tablename__ = 'cancelled_bills'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    shift_id = Column(Integer, nullable=True)
    reason = Column(Text, nullable=False)
    total_amount = Column(Float, nullable=False, default=0.0)
    items_json = Column(Text, nullable=False)
    created_at = Column(DateTime, default=datetime.utcnow)

def init_db(db_path='sqlite:///pos_offline.db'):
    engine = create_engine(db_path, connect_args={'check_same_thread': False})
    
    # Simple migrations
    try:
        with engine.connect() as conn:
            conn.execute(text("ALTER TABLE products ADD COLUMN barcode VARCHAR(50)"))
    except Exception:
        pass
    try:
        with engine.connect() as conn:
            conn.execute(text("ALTER TABLE receipts ADD COLUMN shift_id INTEGER"))
    except Exception:
        pass
    try:
        with engine.connect() as conn:
            conn.execute(text("ALTER TABLE receipts ADD COLUMN doc_number VARCHAR(50)"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN sync_status VARCHAR(20) DEFAULT 'pending'"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN sync_error TEXT"))
    except Exception:
        pass
        
    try:
        with engine.connect() as conn:
            conn.execute(text("ALTER TABLE receipts ADD COLUMN cash_amount FLOAT DEFAULT 0.0"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN transfer_amount FLOAT DEFAULT 0.0"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN is_full_tax BOOLEAN DEFAULT 0"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN customer_name VARCHAR(255)"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN customer_tax_id VARCHAR(50)"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN customer_address TEXT"))
    except Exception:
        pass
        
    Base.metadata.create_all(engine)
    Session = sessionmaker(bind=engine)
    return Session()
