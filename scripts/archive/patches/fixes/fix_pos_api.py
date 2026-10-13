with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace(
    '\->cashierCandidates(\, null, \->user_id)'.replace('\\$', '$'),
    '\->cashierCandidates(\, null, null)'.replace('\\$', '$')
)
c = c.replace(
    '\->cashierCandidates(\, \[\'code\'] ?? null, \->user_id)'.replace('\\$', '$'),
    '\->cashierCandidates(\, \[\'code\'] ?? null, null)'.replace('\\$', '$')
)
c = c.replace(
    '\->cashierCandidates(\, \[\'code\'], \->user_id)'.replace('\\$', '$'),
    '\->cashierCandidates(\, \[\'code\'], null)'.replace('\\$', '$')
)

with open(r'd:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php', 'w', encoding='utf-8') as f:
    f.write(c)
