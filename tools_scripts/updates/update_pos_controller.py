import sys

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_validation = '''            'vat_mode' => ['nullable', 'string', 'in:included,excluded'],
            'items' => ['required', 'array', 'min:1'],'''

new_validation = '''            'vat_mode' => ['nullable', 'string', 'in:included,excluded'],
            'is_full_tax' => ['nullable', 'boolean'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_tax_id' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],'''

content = content.replace(old_validation, new_validation)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated PosController validation")
