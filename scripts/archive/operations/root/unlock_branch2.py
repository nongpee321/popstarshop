import re
with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'r', encoding='utf-8') as f:
    c = f.read()

c = re.sub(
    r'(private function cashierCandidates\(\?int \, \?string \ = null, \?int \ = null\)\s*\{)',
    r'\1\n         = null; // ALWAYS allow all branches for POS login\n',
    c
)

with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'w', encoding='utf-8') as f:
    f.write(c)
