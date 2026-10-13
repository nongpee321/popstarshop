import re
file_path = r"d:\pop-erp-food\ERPPOP-main\app\Http\Middleware\ErpAuthorize.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_code = """        if ($user->must_change_password && ! in_array($routeName, self::PASSWORD_CHANGE_ROUTES, true)) {
            return redirect()->route('password.change');
        }"""
        
new_code = """        // ยกเลิกการบังคับเปลี่ยนรหัสผ่านตามที่ผู้ใช้ร้องขอ
        // if ($user->must_change_password && ! in_array($routeName, self::PASSWORD_CHANGE_ROUTES, true)) {
        //     return redirect()->route('password.change');
        // }"""

content = content.replace(old_code, new_code)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Disabled forced password change in middleware.")
