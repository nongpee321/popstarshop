import re
with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'r', encoding='utf-8') as f:
    c = f.read()

c = re.sub(r'\s*=\s*null;\s*return Salesman::query\(\)', '\n        $branchId = null;\n        return Salesman::query()', c)

with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'w', encoding='utf-8') as f:
    f.write(c)
