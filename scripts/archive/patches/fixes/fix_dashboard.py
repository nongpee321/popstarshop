import re

file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix AR card
content = content.replace("฿{{ number_format($summary->total_ar, 2) }}", "฿{{ number_format($receivables->open_amount, 2) }}")
content = content.replace("เอกสารค้าง: <span class=\"text-dark fw-bold\">0 รายการ</span>", "เอกสารค้าง: <span class=\"text-dark fw-bold\">{{ number_format($receivables->open_count) }} รายการ</span>")
content = content.replace("เกินกำหนดชำระ: <span class=\"text-success fw-bold\">฿0.00</span>", "เกินกำหนดชำระ: <span class=\"{{ $receivables->overdue_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold\">฿{{ number_format($receivables->overdue_amount, 2) }}</span>")

# Fix POS card
content = content.replace("{{ number_format($summary->total_sales, 2) }} ฿", "{{ number_format($posTerminalSummary->sum('amount'), 2) }} ฿")
content = content.replace("ส่วนลด POS: <span class=\"text-dark fw-bold\">฿0.00</span>", "ส่วนลด POS: <span class=\"text-dark fw-bold\">฿{{ number_format($summary->total_discount, 2) }}</span>")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed variable names in dashboard.blade.php")
