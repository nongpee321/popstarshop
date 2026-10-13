<?php
require __DIR__ . '/vendor/autoload.php';
\ = require_once __DIR__ . '/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

\ = \App\Models\Branch::all();
foreach (\ as \) {
    echo "Branch: " . \->name_th . " | Default Loc: " . \->default_warehouse_location_id . "\n";
}

\ = \App\Models\Product::where('sku_code', 'SKU-MLSWOV')->first();
\ = \App\Models\StockBalance::where('product_id', \->id)->get();
foreach (\ as \) {
    echo "Loc: " . \->warehouse_location_id . " | Qty: " . \->on_hand_qty . "\n";
}
