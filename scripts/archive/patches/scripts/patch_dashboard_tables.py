import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Update Top 10 table
old_top_row = """                        <tr>
                            <td class="fw-bold dash-text">{{ $item->name_th }}</td>
                            <td class="dash-muted fs-7">{{ $item->sku_code ?: '-' }}</td>"""
                            
new_top_row = """                        <tr>
                            <td>
                                <div class="fw-bold dash-text">{{ $item->name_th }}</div>
                                <div class="dash-muted" style="font-size: 0.7rem;"><i class="bi bi-shop"></i> {{ $item->branch_name }}</div>
                            </td>
                            <td class="dash-muted fs-7 align-middle">{{ $item->sku_code ?: '-' }}</td>"""
content = content.replace(old_top_row, new_top_row)

# Make sure other columns in Top 10 are vertically aligned
content = content.replace("""<td class="text-end fw-bold">{{ number_format($item->total_qty, 0) }}</td>""", """<td class="text-end fw-bold align-middle">{{ number_format($item->total_qty, 0) }}</td>""")
content = content.replace("""<td class="text-end fw-bold">฿{{ number_format($item->total_amount, 2) }}</td>""", """<td class="text-end fw-bold align-middle">฿{{ number_format($item->total_amount, 2) }}</td>""")

# Update Low Stock table
old_low_row = """                        <tr>
                            <td class="fw-bold dash-text">{{ $item->name_th ?? '-' }}</td>
                            <td class="dash-muted fs-7">{{ $item->sku_code ?? '-' }}</td>
                            <td class="dash-muted fs-7">{{ $item->location_name ?? '-' }}</td>"""
                            
new_low_row = """                        <tr>
                            <td>
                                <div class="fw-bold dash-text">{{ $item->name_th ?? '-' }}</div>
                                <div class="dash-muted" style="font-size: 0.7rem;"><i class="bi bi-shop"></i> สาขา: {{ $item->branch_name ?? '-' }}</div>
                            </td>
                            <td class="dash-muted fs-7 align-middle">{{ $item->sku_code ?? '-' }}</td>
                            <td class="dash-muted fs-7 align-middle">{{ $item->location_name ?? '-' }}</td>"""
content = content.replace(old_low_row, new_low_row)
content = content.replace("""<td class="text-end">
                                <span class="badge rounded-pill dash-bg-red-soft px-3 py-2 fs-6">{{ number_format($item->on_hand_qty, 0) }}</span>
                            </td>""", """<td class="text-end align-middle">
                                <span class="badge rounded-pill dash-bg-red-soft px-3 py-2 fs-6">{{ number_format($item->on_hand_qty, 0) }}</span>
                            </td>""")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated dashboard tables in view.")
