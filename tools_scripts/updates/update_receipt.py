import sys
import re

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Make a backup just in case
with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog_backup.py', 'w', encoding='utf-8') as f:
    f.write(content)

old_print_def = '''def print_escpos_receipt(printer_name, receipt_data, config):
    def enc(text):
        try:
            return text.encode('tis-620', errors='replace')
        except LookupError:
            return text.encode('cp874', errors='replace')
    
    header_text = config.get('receipt_header', '')
    if not header_text:
        header_text = "บริษัท ป๊อปสตาร์ฟู้ดส์ เทรดดิ้ง จำกัด\\n480 ถนนวารินชำราบ-ศรีสะเกษ ม.1 ต.แสนสุข\\nอ.วารินชำราบ จ.อุบลราชธานี 34190\\nโทร. 061-935-4497 | Line: @popstarshop"
        
    footer_text = config.get('receipt_footer', '')
    if not footer_text:
        footer_text = "ขอบคุณที่ใช้บริการ\\nโอกาสหน้าเชิญใหม่ครับ/ค่ะ"

    branch = receipt_data.get('branch', 'สำนักงานใหญ่')
    cashier = receipt_data.get('cashier', 'พนักงาน')
    receipt_no = receipt_data.get('doc_number', '-')
    date_str = receipt_data.get('date', '-')
    total_amount = receipt_data.get('total', 0.0)
    method_str = "เงินสด" if receipt_data.get('payment_method') == 'cash' else "โอนเงิน/QR"
    
    ESC = b'\\x1b'
    GS  = b'\\x1d'
    INIT          = ESC + b'@'
    ALIGN_LEFT    = ESC + b'a\\x00'
    ALIGN_CENTER  = ESC + b'a\\x01'
    ALIGN_RIGHT   = ESC + b'a\\x02'
    BOLD_ON       = ESC + b'E\\x01'
    BOLD_OFF      = ESC + b'E\\x00'
    DOUBLE_ON     = GS  + b'!\\x11'
    DOUBLE_OFF    = GS  + b'!\\x00'
    LINE_FEED     = b'\\n'
    CUT_PAPER     = GS  + b'V\\x41\\x03'
    
    cmds = bytearray()
    cmds += INIT
    
    cmds += ALIGN_CENTER
    
    for line in header_text.split('\\n'):
        cmds += enc(line) + LINE_FEED
        
    cmds += LINE_FEED
    cmds += DOUBLE_ON + BOLD_ON + enc("ใบเสร็จรับเงินอย่างย่อ") + DOUBLE_OFF + BOLD_OFF + LINE_FEED
    cmds += LINE_FEED
    
    cmds += ALIGN_LEFT
    cmds += enc(f"สาขา: {branch}") + LINE_FEED
    cmds += enc(f"เครื่อง POS: 01") + LINE_FEED
    cmds += enc(f"พนักงาน: {cashier}") + LINE_FEED
    cmds += enc(f"เลขที่: {receipt_no}") + LINE_FEED
    cmds += enc(f"วันที่: {date_str}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED'''

# The text contains thai encoding, let's just use re to replace everything before or item in receipt_data.get('items', []):

old_part = content[:content.find("    for item in receipt_data.get('items', []):")]

new_part = '''def print_escpos_receipt(printer_name, receipt_data, config):
    def enc(text):
        try:
            return text.encode('tis-620', errors='replace')
        except LookupError:
            return text.encode('cp874', errors='replace')
    
    header_text = config.get('receipt_header', '')
    if not header_text:
        header_text = "บริษัท ป๊อปสตาร์ฟู้ดส์ เทรดดิ้ง จำกัด\\n480 ถนนวารินชำราบ-ศรีสะเกษ ม.1 ต.แสนสุข\\nอ.วารินชำราบ จ.อุบลราชธานี 34190\\nโทร. 061-935-4497 | Line: @popstarshop"
        
    footer_text = config.get('receipt_footer', '')
    if not footer_text:
        footer_text = "ขอบคุณที่ใช้บริการ\\nโอกาสหน้าเชิญใหม่ครับ/ค่ะ"

    branch = receipt_data.get('branch', 'สำนักงานใหญ่')
    cashier = receipt_data.get('cashier', 'พนักงาน')
    receipt_no = receipt_data.get('doc_number', '-')
    date_str = receipt_data.get('date', '-')
    total_amount = receipt_data.get('total', 0.0)
    
    pm = receipt_data.get('payment_method', 'cash')
    if pm == 'cash': method_str = "เงินสด"
    elif pm == 'transfer': method_str = "โอนเงิน/QR"
    elif pm == 'split': method_str = "จ่ายแบบผสม"
    else: method_str = pm
    
    is_full_tax = receipt_data.get('is_full_tax', False)
    cust_name = receipt_data.get('customer_name', '') or ''
    cust_tax = receipt_data.get('customer_tax_id', '') or ''
    cust_addr = receipt_data.get('customer_address', '') or ''
    
    ESC = b'\\x1b'
    GS  = b'\\x1d'
    INIT          = ESC + b'@'
    ALIGN_LEFT    = ESC + b'a\\x00'
    ALIGN_CENTER  = ESC + b'a\\x01'
    ALIGN_RIGHT   = ESC + b'a\\x02'
    BOLD_ON       = ESC + b'E\\x01'
    BOLD_OFF      = ESC + b'E\\x00'
    DOUBLE_ON     = GS  + b'!\\x11'
    DOUBLE_OFF    = GS  + b'!\\x00'
    LINE_FEED     = b'\\n'
    CUT_PAPER     = GS  + b'V\\x41\\x03'
    
    cmds = bytearray()
    cmds += INIT
    
    cmds += ALIGN_CENTER
    
    for line in header_text.split('\\n'):
        cmds += enc(line) + LINE_FEED
        
    cmds += LINE_FEED
    if is_full_tax:
        cmds += DOUBLE_ON + BOLD_ON + enc("ใบกำกับภาษีเต็มรูป") + DOUBLE_OFF + BOLD_OFF + LINE_FEED
        cmds += DOUBLE_ON + BOLD_ON + enc("ใบเสร็จรับเงิน") + DOUBLE_OFF + BOLD_OFF + LINE_FEED
    else:
        cmds += DOUBLE_ON + BOLD_ON + enc("ใบเสร็จรับเงินอย่างย่อ") + DOUBLE_OFF + BOLD_OFF + LINE_FEED
    cmds += LINE_FEED
    
    cmds += ALIGN_LEFT
    if is_full_tax:
        cmds += enc(f"นามลูกค้า: {cust_name}") + LINE_FEED
        cmds += enc(f"เลขผู้เสียภาษี: {cust_tax}") + LINE_FEED
        cmds += enc(f"ที่อยู่: {cust_addr}") + LINE_FEED
        cmds += enc("-" * 48) + LINE_FEED
        
    cmds += enc(f"สาขา: {branch}") + LINE_FEED
    cmds += enc(f"เครื่อง POS: 01") + LINE_FEED
    cmds += enc(f"พนักงาน: {cashier}") + LINE_FEED
    cmds += enc(f"เลขที่: {receipt_no}") + LINE_FEED
    cmds += enc(f"วันที่: {date_str}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
'''

content = new_part + content[content.find("    for item in receipt_data.get('items', []):"):]

# Update the payment section at the end of the receipt
old_payment_section = '''    cmds += enc(f"ยอดสุทธิ (Total)                 {total_amount:10.2f}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
    
    cmds += enc(f"ชำระโดย: {method_str}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
    cmds += ALIGN_CENTER
    for line in footer_text.split('\\n'):'''

new_payment_section = '''    # Calculate VAT (7%)
    vat_amount = total_amount * 7 / 107
    vatable = total_amount - vat_amount
    
    cmds += enc(f"มูลค่าสินค้า/บริการ (Vatable)     {vatable:10.2f}") + LINE_FEED
    cmds += enc(f"ภาษีมูลค่าเพิ่ม (VAT 7%)           {vat_amount:10.2f}") + LINE_FEED
    cmds += enc(f"ยอดสุทธิ (Total)                 {total_amount:10.2f}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
    
    cmds += enc(f"ชำระโดย: {method_str}") + LINE_FEED
    if pm == 'split':
        cmds += enc(f"  - เงินสด:                        {receipt_data.get('cash_amount', 0.0):10.2f}") + LINE_FEED
        cmds += enc(f"  - เงินโอน:                       {receipt_data.get('transfer_amount', 0.0):10.2f}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
    cmds += ALIGN_CENTER
    for line in footer_text.split('\\n'):'''

content = content.replace('''    cmds += enc(f"ยอดสุทธิ (Total)                 {total_amount:10.2f}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
    
    cmds += enc(f"ชำระโดย: {method_str}") + LINE_FEED
    cmds += enc("-" * 48) + LINE_FEED
    cmds += ALIGN_CENTER
    for line in footer_text.split('\\n'):''', new_payment_section)

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated receipt_dialog.py")

