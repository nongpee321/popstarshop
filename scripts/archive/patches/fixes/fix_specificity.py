import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix the specificity issue causing Light Mode to always win
content = content.replace(
    ':root, [data-bs-theme="light"], body:not(.dark-mode) {',
    ':root, html[data-color-mode="light"], [data-bs-theme="light"] {'
)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed CSS specificity bug.")
