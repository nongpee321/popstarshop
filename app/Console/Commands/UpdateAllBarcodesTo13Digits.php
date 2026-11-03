<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductBarcode;
use App\Support\BarcodePolicy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateAllBarcodesTo13Digits extends Command
{
    protected $signature = 'erp:update-barcodes-13-digits';
    protected $description = 'Update all products to have 13-digit EAN-13 barcodes and sku_codes';

    public function handle(): int
    {
        $this->info('Starting 13-digit barcode generation for all products...');

        try {
            DB::transaction(function () {
                // 1. Preserve original sku_code in legacy_sku if not already set
                DB::statement("UPDATE products SET legacy_sku = sku_code WHERE legacy_sku IS NULL OR legacy_sku = ''");

                // 2. Temporarily set sku_code to TMP_{id} to avoid unique constraint collision
                DB::statement("UPDATE products SET sku_code = 'TMP_' || id");

                // 3. Clear old barcodes from product_barcodes
                ProductBarcode::query()->delete();

                // 4. Generate 13-digit EAN-13 barcodes for all products
                $products = Product::orderBy('id')->get();
                $count = 0;

                foreach ($products as $product) {
                    $body = '299' . str_pad((string) $product->id, 9, '0', STR_PAD_LEFT);
                    $checkDigit = $this->ean13CheckDigit($body);
                    $barcode13 = $body . $checkDigit;

                    // Update sku_code
                    $product->sku_code = $barcode13;
                    $product->save();

                    // Create barcode in product_barcodes
                    ProductBarcode::create([
                        'product_id'   => $product->id,
                        'barcode'      => $barcode13,
                        'unit_id'      => $product->base_unit_id,
                        'unit_factor'  => 1,
                        'price'        => $product->default_price,
                        'is_active'    => true,
                        'barcode_type' => BarcodePolicy::INTERNAL_13,
                    ]);

                    $count++;
                }

                $this->info("Successfully updated {$count} products with 13-digit barcodes.");
            });

            // Verification
            $totalProducts = Product::count();
            $totalBarcodes = ProductBarcode::whereRaw('LENGTH(barcode) = 13')->count();
            $this->info("Verification: Total Products = {$totalProducts}, Total 13-digit Barcodes = {$totalBarcodes}");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function ean13CheckDigit(string $twelveDigits): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ((int) $twelveDigits[$i]) * ($i % 2 === 0 ? 1 : 3);
        }
        return (10 - ($sum % 10)) % 10;
    }
}
