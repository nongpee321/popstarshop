with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace(' = null;\n        return Salesman::query()', '$branchId = null;\n        return Salesman::query()')

with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'w', encoding='utf-8') as f:
    f.write(c)
