import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_block = """        if ($matches->count() > 1 && ! isset($data['cashier_id'])) {
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
        }
        $cashier = $matches->firstWhere('id', (int) ($data['cashier_id'] ?? 0));"""

new_block = """        if ($matches->count() > 1 && ! isset($data['cashier_id'])) {
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
content = content.replace(old_block, new_block)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched PosApiController properly")
