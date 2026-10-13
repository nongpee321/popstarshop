import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix Top Products loop
content = content.replace("@forelse($topSelling as $item)", "@forelse($topProducts as $item)")
content = content.replace("{{ $item->sku ?: '-' }}", "{{ $item->sku_code ?: '-' }}")
content = content.replace("{{ number_format($item->qty, 2) }}", "{{ number_format($item->total_qty, 0) }}")

# Fix Low Stock loop
content = content.replace("{{ $item->product->name_th ?? '-' }}", "{{ $item->name_th ?? '-' }}")
content = content.replace("{{ $item->product->code ?? '-' }}", "{{ $item->sku_code ?? '-' }}")
content = content.replace("พื้นที่จัดเก็บ - สำนักงานใหญ่", "{{ $item->location_name ?? '-' }}")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed loops in dashboard.blade.php")
