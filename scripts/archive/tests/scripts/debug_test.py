import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("        if (isset($data['cashier_id']) && ! $cashier) {", "        if ($request->has('cashier_id') && (int)$data['cashier_id'] === 2) {\n            // dd($matches->pluck('id')->toArray(), $assignedUserId, $device?->user_id);\n        }\n        if (isset($data['cashier_id']) && ! $cashier) {")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
