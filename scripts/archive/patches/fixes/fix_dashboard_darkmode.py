import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add strict text colors to fix Dark Mode invisibility issues
css_fixes = """
    /* Fix for Dark Mode: Force text colors inside our light cards */
    .db-header, .kpi-card, .chart-card, .table-card, .filter-bar, .alert-banner {
        color: #1e293b !important;
    }
    h5, .fs-3, .fs-5, .fs-2, .fw-bolder {
        color: #0f172a !important;
    }
    .text-muted {
        color: #64748b !important;
    }
    .text-dark {
        color: #0f172a !important;
    }
    .text-danger {
        color: #e11d48 !important;
    }
    .bg-white {
        background-color: #ffffff !important;
    }
    .bg-light {
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }
"""

content = content.replace("</style>", css_fixes + "\n</style>")

# Fix specific inline issues
content = content.replace("<strong class=\"text-decoration-underline\">{{ $alertCount }} รายการ</strong>", "<strong class=\"text-decoration-underline text-danger\">{{ $alertCount }} รายการ</strong>")
content = content.replace("<strong class=\"text-decoration-underline\">{{ $lowStockCount }} รายการ</strong>", "<strong class=\"text-decoration-underline text-danger\">{{ $lowStockCount }} รายการ</strong>")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Applied dark mode text fixes.")
