import sys
with open(r'D:\pop-erp-food\python-pos\database\models.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Add new columns to PosReceipt class
receipt_class_old = '''class PosReceipt(Base):
    __tablename__ = 'receipts'
    
    id = Column(String(50), primary_key=True) # UUID for offline creation
    shift_id = Column(Integer, nullable=True) # Local shift id
    doc_number = Column(String(50), nullable=True)
    total_amount = Column(Float, nullable=False, default=0.0)
    received_amount = Column(Float, nullable=False, default=0.0)
    payment_method = Column(String(20), nullable=False, default='cash') # cash, transfer, card
    created_at = Column(DateTime, default=datetime.utcnow)
    sync_status = Column(String(20), default='pending') # pending, synced, error
    sync_error = Column(Text, nullable=True)'''

receipt_class_new = '''class PosReceipt(Base):
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
    sync_error = Column(Text, nullable=True)'''

content = content.replace(receipt_class_old, receipt_class_new)

# Add migrations to init_db
init_db_old = '''    try:
        with engine.connect() as conn:
            conn.execute(text("ALTER TABLE receipts ADD COLUMN doc_number VARCHAR(50)"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN sync_status VARCHAR(20) DEFAULT 'pending'"))
            conn.execute(text("ALTER TABLE receipts ADD COLUMN sync_error TEXT"))
    except Exception:
        pass
        
    Base.metadata.create_all(engine)'''

init_db_new = '''    try:
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
        
    Base.metadata.create_all(engine)'''

content = content.replace(init_db_old, init_db_new)

with open(r'D:\pop-erp-food\python-pos\database\models.py', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated models.py")
