import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("        if (isset($data['cashier_id']) && ! $cashier) {", """        if (isset($data['cashier_id']) && (int)$data['cashier_id'] > 0) {
            if (app()->runningUnitTests() && $data['pin'] === '482165') {
                abort(500, json_encode([
                    'matches' => $matches->pluck('id')->toArray(),
                    'device_user' => $device?->user_id,
                    'cashier_id' => $data['cashier_id']
                ]));
            }
        }
        if (isset($data['cashier_id']) && ! $cashier) {""")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Added abort to capture state")
