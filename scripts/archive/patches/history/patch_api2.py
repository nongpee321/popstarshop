import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_code = """        if ($matches->count() > 1 && ! isset($data['cashier_id'])) {
            // 1,o,,"1?,,,'1^,, ,1%,,T,-,,1^1,,S1% PIN ,?,,,, , 1?,S1^,T 1234: 
            return response()->json([
                'success' => true,
                'selection_required' => true,
                'cashiers' => $matches->map(fn (Salesman $candidate) => $this->cashierPayload($candidate))->values(),
            ]);
        }
        $cashier = $matches->firstWhere('id', (int) ($data['cashier_id'] ?? 0));
        if (! $cashier) {
            $cashier = $matches->first();
        }"""

new_code = """        if ($matches->count() > 1 && ! isset($data['cashier_id'])) {
            $assignedMatch = $device?->user_id ? $matches->firstWhere('user_id', $device->user_id) : null;
            if ($assignedMatch) {
                $cashier = $assignedMatch;
            } else {
                return response()->json([
                    'success' => true,
                    'selection_required' => true,
                    'cashiers' => $matches->map(fn (Salesman $candidate) => $this->cashierPayload($candidate))->values(),
                ]);
            }
        } else {
            $cashier = $matches->firstWhere('id', (int) ($data['cashier_id'] ?? 0));
            if (! $cashier) {
                $cashier = $matches->first();
            }
        }"""

content = content.replace(old_code, new_code)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched PosApiController.php cashierLogin tie break")
