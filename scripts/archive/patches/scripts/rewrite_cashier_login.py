import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will write a regex to replace the if/else block for matches->count() > 1
pattern = r'if \(\$matches->count\(\) > 1 && ! isset\(\$data\[\'cashier_id\'\]\)\) \{.*?(?=return \$this->authenticatedCashierResponse)'

new_block = """if ($matches->count() > 1 && ! isset($data['cashier_id'])) {
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
        }

        if (isset($data['cashier_id']) && ! $cashier) {
            return response()->json(['success' => false, 'message' => 'พนักงานที่คุณเลือกไม่มีสิทธิ์ในสาขานี้ หรือรหัส PIN ไม่ถูกต้อง'], 422);
        }

        if (! $cashier) {
            $cashier = $matches->first();
        }

        """

content = re.sub(pattern, new_block, content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Rewrote the cashier selection logic")
