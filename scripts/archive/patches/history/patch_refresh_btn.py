import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\pos\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace button
old_btn = """        <button class="topbar-btn" type="button" onclick="window.location.reload()" title="รีเฟรชหน้าต่าง">
            <i class="bi bi-arrow-clockwise"></i>
        </button>"""
new_btn = """        <button class="topbar-btn" type="button" @click="fastRefresh($event)" title="ซิงค์ข้อมูลล่าสุด">
            <i class="bi bi-arrow-clockwise"></i>
        </button>"""

# Wait, the title was "รีเฟรชหน้าต่าง" ? No, the text in console was `title=",,1?,Y,,S,,T1%,,^,"`.
# I'll just replace the button via regex.
pattern_btn = r'<button class="topbar-btn" type="button" onclick="window\.location\.reload\(\)" title="[^"]*">\s*<i class="bi bi-arrow-clockwise"></i>\s*</button>'
replacement_btn = """<button class="topbar-btn" type="button" @click="fastRefresh($event)" title="ซิงค์ข้อมูลล่าสุด">
            <i class="bi bi-arrow-clockwise"></i>
        </button>"""
content = re.sub(pattern_btn, replacement_btn, content)

# Add fastRefresh method to Alpine
method_code = """        async fastRefresh(e) {
            const btn = e.currentTarget;
            const icon = btn.querySelector('i');
            icon.style.animation = 'spin 0.5s linear infinite';
            btn.disabled = true;
            
            try {
                await Promise.all([
                    this.loadProducts(),
                    this.loadPromotions(),
                    this.loadActiveShift(),
                    this.loadReceiptSettings()
                ]);
                erpToast('success', 'อัปเดตข้อมูลเสร็จสิ้น', { timer: 1500 });
            } finally {
                icon.style.animation = '';
                btn.disabled = false;
            }
        },
        async loadProducts() {"""

content = content.replace("async loadProducts() {", method_code)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Replaced refresh button with fastRefresh")
