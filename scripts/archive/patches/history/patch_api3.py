import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r"if \(\$matches->count\(\) > 1 && ! isset\(\$data\['cashier_id'\]\)\) \{.*?return response\(\)->json\(\[\s*'success' => true,\s*'selection_required' => true,\s*'cashiers' => .*?\n\s*\]\);\s*\}"
replacement = """if ($matches->count() > 1 && ! isset($data['cashier_id'])) {
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
        }"""

new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)

# Now we need to modify how $cashier is assigned below this block
# Because if $assignedMatch was found, $cashier is already set.

pattern2 = r"\}\n\s*\$cashier = \$matches->firstWhere\('id', \(int\) \(\$data\['cashier_id'\] \?\? 0\)\);\n\s*if \(! \$cashier\) \{\n\s*\$cashier = \$matches->first\(\);\n\s*\}"
replacement2 = """}
        
        if (!isset($cashier)) {
            $cashier = $matches->firstWhere('id', (int) ($data['cashier_id'] ?? 0));
            if (! $cashier) {
                $cashier = $matches->first();
            }
        }"""

new_content = re.sub(pattern2, replacement2, new_content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Patched correctly with regex")
