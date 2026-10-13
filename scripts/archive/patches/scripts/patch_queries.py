import re
file_path = r"d:\pop-erp-food\ERPPOP-main\app\Http\Controllers\DashboardController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Update topProducts query
old_top_products = """        $topProducts = DB::table('pos_receipt_items as i')
            ->join('pos_receipts as r', 'r.id', '=', 'i.pos_receipt_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->when($branchId, fn ($q) => $receiptBranchScope($q, 'r'))
            ->whereBetween('r.receipt_date', [$from->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('p.id', 'p.sku_code', 'p.name_th')
            ->orderByDesc(DB::raw('sum(i.net_amount)'))
            ->select('p.sku_code', 'p.name_th', DB::raw('sum(i.qty) as total_qty'), DB::raw('sum(i.net_amount) as total_amount'))
            ->limit(20)
            ->get();"""
            
new_top_products = """        $topProducts = DB::table('pos_receipt_items as i')
            ->join('pos_receipts as r', 'r.id', '=', 'i.pos_receipt_id')
            ->join('pos_terminals as t', 't.id', '=', 'r.pos_terminal_id')
            ->join('branches as b', 'b.id', '=', 't.branch_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->when($branchId, fn ($q) => $q->where('b.id', $branchId))
            ->whereBetween('r.receipt_date', [$from->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('p.id', 'p.sku_code', 'p.name_th', 'b.id', 'b.name_th')
            ->orderByDesc(DB::raw('sum(i.net_amount)'))
            ->select('p.sku_code', 'p.name_th', 'b.name_th as branch_name', DB::raw('sum(i.qty) as total_qty'), DB::raw('sum(i.net_amount) as total_amount'))
            ->limit(20)
            ->get();"""

content = content.replace(old_top_products, new_top_products)

# Update lowStock query
old_low_stock = """        $lowStock = DB::table('stock_balances as sb')
            ->join('products as p', 'p.id', '=', 'sb.product_id')
            ->join('warehouse_locations as wl', 'wl.id', '=', 'sb.warehouse_location_id')
            ->orderBy('sb.on_hand_qty')
            ->select('p.sku_code', 'p.name_th', 'wl.name as location_name', 'sb.on_hand_qty')
            ->limit(20)
            ->get();"""
            
new_low_stock = """        $lowStock = DB::table('stock_balances as sb')
            ->join('products as p', 'p.id', '=', 'sb.product_id')
            ->join('warehouse_locations as wl', 'wl.id', '=', 'sb.warehouse_location_id')
            ->join('warehouses as w', 'w.id', '=', 'wl.warehouse_id')
            ->join('branches as b', 'b.id', '=', 'w.branch_id')
            ->when($branchId, fn ($q) => $q->where('b.id', $branchId))
            ->orderBy('sb.on_hand_qty')
            ->select('p.sku_code', 'p.name_th', 'wl.name as location_name', 'b.name_th as branch_name', 'sb.on_hand_qty')
            ->limit(20)
            ->get();"""

content = content.replace(old_low_stock, new_low_stock)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated queries in DashboardController.")
