<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductBarcode;
use App\Services\InternalEan13Allocator;
use App\Support\BarcodePolicy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class EnsureInternalEan13Barcodes extends Command
{
    protected $signature = 'products:ensure-internal-ean13
        {--confirm-database= : Database name required with --apply}
        {--apply : Add an internal EAN-13 barcode to products without one}
        {--force : Skip the interactive confirmation}';

    protected $description = 'Add scannable internal EAN-13 barcodes without changing product SKUs';

    public function handle(InternalEan13Allocator $allocator): int
    {
        $database = DB::connection()->getDatabaseName();
        if ($this->option('apply') && $this->option('confirm-database') !== $database) {
            $this->error("ต้องระบุ --confirm-database={$database} ให้ตรงกับฐานที่ต่ออยู่ก่อนใช้ --apply");
            return self::FAILURE;
        }

        $policy = app(BarcodePolicy::class);
        $products = Product::withTrashed()->with(['barcodes' => fn ($query) => $query->where('is_active', true)])->get();
        $missing = $products->filter(function (Product $product) use ($policy): bool {
            return ! $product->barcodes->contains(function (ProductBarcode $barcode) use ($policy): bool {
                return $barcode->barcode_type !== BarcodePolicy::SCALE_WEIGHT
                    && $policy->isValidEan13((string) $barcode->barcode);
            });
        })->values();

        $this->line("ฐานข้อมูล: {$database}");
        $this->table(['รายการ', 'จำนวน'], [
            ['สินค้าทั้งหมด', $products->count()],
            ['ต้องเพิ่ม EAN-13 ภายใน', $missing->count()],
            ['มี EAN-13 ใช้งานได้อยู่แล้ว', $products->count() - $missing->count()],
        ]);

        if (! $this->option('apply')) {
            $this->info('ยังไม่มีการเปลี่ยนข้อมูล ใช้ --apply พร้อมระบุชื่อฐานข้อมูลเพื่อดำเนินการ');
            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('เพิ่มบาร์โค้ด EAN-13 ภายในให้สินค้าที่ขาด โดยคง SKU เดิมไว้หรือไม่?', false)) {
            return self::SUCCESS;
        }

        try {
            $created = DB::transaction(function () use ($missing, $allocator, $policy): int {
                $created = 0;
                foreach ($missing as $product) {
                    $hasEan13 = $product->barcodes->contains(fn (ProductBarcode $barcode): bool =>
                        $barcode->barcode_type !== BarcodePolicy::SCALE_WEIGHT
                        && $policy->isValidEan13((string) $barcode->barcode)
                    );
                    if ($hasEan13) {
                        continue;
                    }

                    $product->barcodes()->create([
                        'barcode' => $allocator->next(),
                        'barcode_type' => BarcodePolicy::INTERNAL_13,
                        'unit_id' => $product->base_unit_id,
                        'unit_factor' => 1,
                        'is_active' => true,
                    ]);
                    $created++;
                }

                return $created;
            });
        } catch (Throwable $error) {
            $this->error('ไม่สำเร็จ: '.$error->getMessage());
            return self::FAILURE;
        }

        $this->info("เพิ่มบาร์โค้ด EAN-13 ภายในแล้ว {$created} รายการ");
        $this->warn('SKU เดิม, ราคา, สต็อก และบาร์โค้ดเดิมไม่ได้ถูกเปลี่ยน');

        return self::SUCCESS;
    }
}
