import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix the child div in alert banner
content = content.replace('<div style="color: #9f1239 !important;" class="alert-banner mb-3">\n    <div>', '<div style="color: #9f1239 !important;" class="alert-banner mb-3">\n    <div style="color: #9f1239 !important;">')

# Fix strong tags in alert banner
content = content.replace('<strong class="text-decoration-underline text-danger">', '<strong class="text-decoration-underline text-danger" style="color: #e11d48 !important;">')

# Fix any stray text-danger classes
content = re.sub(r'class="([^"]*)text-danger([^"]*)"', r'class="\1text-danger\2" style="color: #e11d48 !important;"', content)

# Check the other alert banner (Extra Banner)
content = content.replace('<div class="alert-banner mb-4 py-3 bg-white" style="border: 1px solid var(--theme-red-light);">\n    <div class="d-flex align-items-center gap-3">\n        <div class="bg-danger text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">',
'<div class="alert-banner mb-4 py-3 bg-white" style="border: 1px solid var(--theme-red-light); color: #0f172a !important;">\n    <div class="d-flex align-items-center gap-3">\n        <div class="bg-danger text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; color: #ffffff !important;">')

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Applied deep inline styles.")
