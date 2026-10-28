<?php

namespace App\Services;

use App\Models\ProductBarcode;
use App\Support\BarcodePolicy;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InternalEan13Allocator
{
    public function next(): string
    {
        if (DB::transactionLevel() === 0) {
            throw new RuntimeException('ต้องเรียกสร้าง EAN-13 ภายใน transaction');
        }

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('SELECT pg_advisory_xact_lock(299013)');
        }

        $last = ProductBarcode::query()
            ->where('barcode', 'like', '299%')
            ->whereRaw('LENGTH(barcode) = 13')
            ->orderByDesc('barcode')
            ->lockForUpdate()
            ->value('barcode');

        $sequence = $last ? ((int) substr((string) $last, 3, 9)) + 1 : 1;
        while ($sequence <= 999999999) {
            $body = '299'.str_pad((string) $sequence, 9, '0', STR_PAD_LEFT);
            $barcode = $body.app(BarcodePolicy::class)->checkDigit($body);
            if (! ProductBarcode::where('barcode', $barcode)->exists()) {
                return $barcode;
            }
            $sequence++;
        }

        throw new RuntimeException('เลขบาร์โค้ด EAN-13 ภายในร้านเต็มแล้ว');
    }
}
