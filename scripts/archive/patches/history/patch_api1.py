import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_code = """        $device?->markCashierVerified($cashier);

        // active_cashier_id"""

new_code = """        $mustChange = $this->mustChangePin($cashier);
        if (!$mustChange) {
            $device?->markCashierVerified($cashier);
        }

        // active_cashier_id"""

content = content.replace(old_code, new_code)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched PosApiController.php markCashierVerified")
