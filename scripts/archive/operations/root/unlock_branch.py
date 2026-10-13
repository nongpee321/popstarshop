with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace(
    'private function cashierCandidates(?int , ?string  = null, ?int  = null)\n    {',
    'private function cashierCandidates(?int , ?string  = null, ?int  = null)\n    {\n         = null; // UNLOCK ALL BRANCHES\n'
)

with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'w', encoding='utf-8') as f:
    f.write(c)
