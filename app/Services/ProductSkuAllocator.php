<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Support\BarcodePolicy;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductSkuAllocator
{
    private const INTERNAL_PREFIX = '299';
    private const MAX_SEQUENCE = 999999999;

    public function nextForCategory(int $categoryId): string
    {
        $category = ProductCategory::query()->findOrFail($categoryId);
        $code = trim((string) $category->code);

        if ($code === '' || $code === '0') {
            throw new RuntimeException('ต้องเลือกประเภทสินค้าก่อน ระบบจึงจะรันรหัสสินค้าได้');
        }
        if ($code === 'CC') {
            throw new RuntimeException('หมวดสินค้ายกเลิกใช้สำหรับเก็บประวัติเท่านั้น ไม่สามารถเพิ่มสินค้าใหม่ได้');
        }

        return $this->next();
    }

    public function next(): string
    {
        if (DB::transactionLevel() === 0) {
            throw new RuntimeException('ต้องเรียกสร้าง SKU ภายใน transaction');
        }

        $sequence = DB::table('product_sku_sequences')->where('id', 1)->lockForUpdate()->first();
        if ($sequence === null) {
            throw new RuntimeException('ไม่พบตัวนับ SKU กรุณารัน migration ก่อน');
        }

        $next = (int) $sequence->last_number + 1;
        while ($next <= self::MAX_SEQUENCE) {
            $body = self::INTERNAL_PREFIX.str_pad((string) $next, 9, '0', STR_PAD_LEFT);
            $sku = $body.app(BarcodePolicy::class)->checkDigit($body);
            $exists = Product::withTrashed()
                ->where('sku_code', $sku)
                ->orWhere('legacy_sku', $sku)
                ->exists()
                || ProductBarcode::where('barcode', $sku)->exists();

            if (! $exists) {
                DB::table('product_sku_sequences')->where('id', 1)->update(['last_number' => $next]);

                return $sku;
            }

            $next++;
        }

        throw new RuntimeException('รหัสสินค้า EAN-13 ภายในร้านเต็มแล้ว');
    }
}
