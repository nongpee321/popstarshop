<?php

namespace App\Services;

use App\Models\MasterCutoverRun;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Support\BarcodePolicy;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** One-time, reviewable migration of every product SKU to its unique EAN-13. */
class ProductSkuRecodeService
{
    public const SCOPE = 'product-ean13-sku';

    /** @return array<int, array{id:int,old:string,legacy:string,new:?string,category:string,name:string,unit_id:?int,barcodes:int}> */
    public function plan(): array
    {
        $barcodeCounts = DB::table('product_barcodes')->selectRaw('product_id, count(*) as total')
            ->groupBy('product_id')->pluck('total', 'product_id');

        $products = Product::withTrashed()->with([
            'category',
            'barcodes' => fn ($query) => $query->where('is_active', true),
        ])->orderBy('id')->get();
        $policy = app(BarcodePolicy::class);
        $plan = [];
        foreach ($products as $product) {
            $category = trim((string) ($product->category?->code ?? ''));
            $ean13 = $product->barcodes
                ->filter(fn (ProductBarcode $barcode): bool => $barcode->barcode_type !== BarcodePolicy::SCALE_WEIGHT
                    && $policy->isValidEan13((string) $barcode->barcode))
                ->sortBy(fn (ProductBarcode $barcode): int => match ($barcode->barcode_type) {
                    BarcodePolicy::EAN13_STANDARD => 0,
                    BarcodePolicy::INTERNAL_13 => 1,
                    default => 2,
                })
                ->first()?->barcode;

            $plan[] = [
                'id' => $product->id,
                'old' => (string) $product->sku_code,
                'legacy' => (string) ($product->legacy_sku ?: $product->sku_code),
                'new' => $ean13,
                'category' => $category,
                'name' => (string) $product->name_th,
                'unit_id' => $product->base_unit_id ? (int) $product->base_unit_id : null,
                'barcodes' => (int) ($barcodeCounts[$product->id] ?? 0),
            ];
        }

        return $plan;
    }

    /** @return array<int, array{level:string,issue:string,detail:string}> */
    public function preflight(array $plan = []): array
    {
        $plan = $plan ?: $this->plan();
        $problems = [];

        if (MasterCutoverRun::where('scope', self::SCOPE)->exists()) {
            $problems[] = ['level' => 'หยุด', 'issue' => 'เปลี่ยนรหัสสินค้าไปแล้ว', 'detail' => 'ห้ามรันซ้ำเพื่อป้องกันรหัสขยับ'];
        }

        $missingEan = collect($plan)->filter(fn (array $row): bool => $row['new'] === null);
        if ($missingEan->isNotEmpty()) {
            $problems[] = [
                'level' => 'หยุด',
                'issue' => 'สินค้าบางรายการไม่มี EAN-13 ที่ตรวจสอบได้',
                'detail' => implode(', ', $missingEan->take(10)->pluck('old')->all()),
            ];
        }

        $duplicates = collect($plan)->pluck('new')->filter()->duplicates();
        foreach ($duplicates->unique() as $sku) {
            $problems[] = ['level' => 'หยุด', 'issue' => 'รหัส SKU ใหม่ซ้ำ', 'detail' => (string) $sku];
        }

        $targets = collect($plan)->pluck('new')->filter()->values();
        $skuDuplicates = Product::withTrashed()->whereIn('sku_code', $targets)->pluck('id', 'sku_code');
        foreach ($plan as $row) {
            if ($row['new'] && isset($skuDuplicates[$row['new']]) && (int) $skuDuplicates[$row['new']] !== $row['id']) {
                $problems[] = ['level' => 'หยุด', 'issue' => 'EAN-13 ซ้ำกับ SKU ปัจจุบันของสินค้าอื่น', 'detail' => (string) $row['new']];
            }

            if ($row['old'] !== '' && $row['old'] !== $row['new']) {
                if ($row['unit_id'] === null) {
                    $problems[] = ['level' => 'หยุด', 'issue' => 'สินค้าไม่มีหน่วยหลักสำหรับเก็บ SKU เดิม', 'detail' => $row['old']];
                    continue;
                }

                $barcodeOwner = DB::table('product_barcodes')->where('barcode', $row['old'])->value('product_id');
                if ($barcodeOwner !== null && (int) $barcodeOwner !== $row['id']) {
                    $problems[] = ['level' => 'หยุด', 'issue' => 'SKU เดิมชนกับบาร์โค้ดของสินค้าอื่น', 'detail' => $row['old']];
                }
            }
        }

        return $problems;
    }

    /** @return array{products:int,first_code:string,last_code:string} */
    public function apply(?int $userId = null): array
    {
        return DB::transaction(function () use ($userId): array {
            DB::table('product_sku_sequences')->where('id', 1)->lockForUpdate()->firstOrFail();
            $plan = $this->plan();
            $blocking = collect($this->preflight($plan))->where('level', 'หยุด');
            if ($blocking->isNotEmpty()) {
                throw new RuntimeException('ยังเปลี่ยนรหัสไม่ได้: '.$blocking->pluck('issue')->unique()->implode(' · '));
            }
            $recodeRows = collect($plan)->values()->all();

            // sku_code unique: ย้ายทุกแถวไปชื่อชั่วคราวก่อน เพื่อให้สลับ/เรียงใหม่ได้โดยไม่ชน
            foreach ($recodeRows as $row) {
                Product::withTrashed()->whereKey($row['id'])->update([
                    'sku_code' => '__recode_'.$row['id'],
                    'legacy_sku' => $row['legacy'],
                ]);
            }
            foreach ($recodeRows as $row) {
                if ($row['old'] === '' || $row['old'] === $row['new']) {
                    continue;
                }

                $oldSkuBarcode = ProductBarcode::where('barcode', $row['old'])->first();
                if ($oldSkuBarcode === null) {
                    ProductBarcode::create([
                        'product_id' => $row['id'],
                        'barcode' => $row['old'],
                        'barcode_type' => BarcodePolicy::CUSTOM,
                        'unit_id' => $row['unit_id'],
                        'unit_factor' => 1,
                        'is_active' => true,
                    ]);
                }
            }
            foreach ($recodeRows as $row) {
                Product::withTrashed()->whereKey($row['id'])->update(['sku_code' => $row['new']]);
            }

            $maxInternalSequence = collect($recodeRows)
                ->pluck('new')
                ->filter(fn (?string $sku): bool => $sku !== null && preg_match('/^299\d{10}$/', $sku) === 1)
                ->map(fn (string $sku): int => (int) substr($sku, 3, 9))
                ->max() ?? 0;
            DB::table('product_sku_sequences')->where('id', 1)->update([
                'last_number' => max((int) DB::table('product_sku_sequences')->where('id', 1)->value('last_number'), $maxInternalSequence),
            ]);

            MasterCutoverRun::create([
                'scope' => self::SCOPE,
                'mapped_count' => count($recodeRows),
                'first_code' => collect($recodeRows)->pluck('new')->filter()->sort()->first(),
                'last_code' => collect($recodeRows)->pluck('new')->filter()->sort()->last(),
                'applied_by' => $userId,
                'applied_at' => now(),
            ]);

            return [
                'products' => count($recodeRows),
                'first_code' => (string) collect($recodeRows)->pluck('new')->filter()->sort()->first(),
                'last_code' => (string) collect($recodeRows)->pluck('new')->filter()->sort()->last(),
            ];
        });
    }

}
