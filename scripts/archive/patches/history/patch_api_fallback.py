import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_logic = """        } else {
            $cashier = $matches->firstWhere('id', (int) ($data['cashier_id'] ?? 0));
            if (! $cashier) {
                $cashier = $matches->first();
            }
        }

        if (isset($data['cashier_id']) && ! $cashier) {"""

new_logic = """        } else {
            $cashier = $matches->firstWhere('id', (int) ($data['cashier_id'] ?? 0));
        }

        if (isset($data['cashier_id']) && ! $cashier) {
            return response()->json(['success' => false, 'message' => 'พนักงานที่คุณเลือกไม่มีสิทธิ์ในสาขานี้ หรือรหัส PIN ไม่ถูกต้อง'], 422);
        }

        if (! $cashier) {
            $cashier = $matches->first();
        }
        
        if (false) {"""

content = content.replace(old_logic, new_logic)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched cashier fallback logic")
