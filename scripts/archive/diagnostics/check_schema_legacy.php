<?php
use Illuminate\Support\Facades\DB;
$columns = DB::select("SELECT column_name, is_nullable FROM information_schema.columns WHERE table_name = 'pos_devices'");
foreach ($columns as $col) {
    echo $col->column_name . ' : ' . $col->is_nullable . PHP_EOL;
}
