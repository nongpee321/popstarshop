with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()
for i, line in enumerate(lines):
    if '\\ = null;' in line or '= null;' in line:
        lines[i] = '        $branchId = null;\n'
with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'w', encoding='utf-8') as f:
    f.writelines(lines)
