import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add Chart.js
content = content.replace("<script>\n    let dashboardRefreshSeconds", "<script src=\"{{ asset('vendor/chartjs/chart.umd.js') }}\"></script>\n<script>\n    let dashboardRefreshSeconds")

# Reduce font sizes and weights
content = content.replace("fs-2 fw-bolder", "fs-3 fw-bold")
content = content.replace("fs-5", "fs-6")
content = content.replace("fw-bolder", "fw-bold")
# Change some fw-bold to fw-medium or normal for less thickness
content = content.replace("text-muted fw-bold", "text-muted fw-semibold")
content = content.replace("fw-bold fs-7", "fw-semibold fs-7")
content = content.replace("h5 class=\"mb-0 fw-bold\"", "h5 class=\"mb-0 fw-semibold\"")

# Fix the styling classes in blade (bootstrap doesn't have fw-semibold by default in older versions but it does in v5. Let's use fw-semibold or standard CSS if needed, Bootstrap 5 has fw-semibold).
# Actually, I'll just change 'fw-bold' to '' for the KPI labels to make them thinner.
content = content.replace("<div class=\"text-muted fw-bold\">", "<div class=\"text-muted fw-semibold\">")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Applied font and chart fixes.")
