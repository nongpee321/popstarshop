<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Services\ProductSkuAllocator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PosOfflineProductController extends Controller
{
    public function store(Request $request, ProductSkuAllocator $skuAllocator): JsonResponse
    {
        Log::info('PosOfflineProductController::store called', $request->all());
        
        try {
            $data = $request->validate([
                'name_th' => 'required|string|max:255',
                'default_price' => 'required|numeric|min:0',
                'barcode' => 'nullable|string|max:50',
            ]);

            $barcode = $data['barcode'] ?? null;

            // Check for a previous legacy product with the same barcode before creating a new one.
            $product = $barcode ? Product::withTrashed()
                ->where(fn ($query) => $query->where('sku_code', $barcode)->orWhere('legacy_sku', $barcode))
                ->first() : null;
            if (!$product && !empty($data['barcode'])) {
                $barcodeRecord = ProductBarcode::where('barcode', $data['barcode'])->first();
                if ($barcodeRecord) {
                    $product = Product::withTrashed()->find($barcodeRecord->product_id);
                }
            }

            if ($product) {
                if ($product->trashed()) {
                    $product->restore();
                }
                return response()->json(['success' => true, 'product_id' => $product->id]);
            }

            // Just create a simple product
            $product = DB::transaction(function () use ($data, $skuAllocator): Product {
                $product = Product::create([
                    'name_th' => $data['name_th'],
                    'name_en' => $data['name_th'],
                    'sku_code' => $skuAllocator->next(),
                    'default_price' => $data['default_price'],
                    'base_unit_id' => \App\Models\ProductUnit::first()?->id ?? 1,
                    'is_active' => true,
                ]);

                if (! empty($data['barcode'])) {
                    DB::insert('insert into product_barcodes (product_id, barcode, unit_id, barcode_type, unit_factor, price, is_active) values (?, ?, ?, ?, ?, ?, ?)', [
                        $product->id,
                        $data['barcode'],
                        $product->base_unit_id ?? 1,
                        'code128',
                        1,
                        $data['default_price'],
                        true,
                    ]);
                }

                return $product;
            });

            Log::info('PosOfflineProductController created product', ['product_id' => $product->id]);
            
            return response()->json([
                'success' => true,
                'product_id' => $product->id,
            ]);
        } catch (\Exception $e) {
            Log::error('PosOfflineProductController ERROR: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
