import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\erp-mockup\pos-orders\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add styles if not present
if '@keyframes spin' not in content:
    content = content.replace("@section('content')", "@section('content')\n<style>\n@keyframes spin { 100% { transform: rotate(360deg); } }\n</style>")

old_btn = """                    @if($order['sync'] === 'failed')
                        <button class="pop-btn" style="padding:4px 8px;color:var(--pop-primary)"><i class="bi bi-arrow-repeat"></i></button>
                    @endif"""

new_btn = """                    @if($order['sync'] === 'failed')
                        <button type="button" class="pop-btn" style="padding:4px 8px;color:var(--pop-primary)" 
                            onclick="const btn = this; btn.innerHTML = '<i class=\\'bi bi-arrow-repeat\\' style=\\'display:inline-block; animation: spin 1s linear infinite\\'></i>'; btn.disabled=true; setTimeout(() => { btn.outerHTML = '<span style=\\'font-size:12px;color:#16a34a;margin-left:5px;font-weight:bold;\\'><i class=\\'bi bi-check-circle-fill\\'></i> เสร็จสิ้น</span>'; }, 1000)">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    @endif"""

content = content.replace(old_btn, new_btn)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched erp mockup index")
