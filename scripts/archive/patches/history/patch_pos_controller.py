import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix enforcedCashierId
content = content.replace(
    "$resolvedId = $this->validatedCashierId(auth()->user()?->posCashierProfile?->id ?: $requested);",
    "$resolvedId = $this->validatedCashierId(auth()->user()?->salesman_id ?: auth()->user()?->posCashierProfile?->id ?: $requested);"
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched PosController enforcedCashierId")
