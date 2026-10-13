import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\pos\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Make reason required
content = content.replace("placeholder=\"หมายเหตุ / ผู้นำส่ง (ถ้ามี)\"", "placeholder=\"หมายเหตุ / ชื่อผู้นำส่ง (ต้องระบุ)\"")
content = content.replace("return { amount: amount, reason: reason };", """if (!reason.trim()) {
                        Swal.showValidationMessage('กรุณาระบุหมายเหตุหรือชื่อผู้นำส่ง');
                        return false;
                    }
                    return { amount: amount, reason: reason };""")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Made reason required")
