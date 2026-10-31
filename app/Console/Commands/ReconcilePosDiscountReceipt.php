<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\PosReceipt;
use App\Models\PosReceiptDiscount;
use App\Services\Accounting\GlPostingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcilePosDiscountReceipt extends Command
{
    protected $signature = 'pos:reconcile-receipt {receipt_no} {discount_amount} {--reason=ปรับปรุงส่วนลดตามใบเสร็จจริง}';

    protected $description = 'ปรับปรุงยอดส่วนลดและยอดสุทธิของบิล POS ย้อนหลังให้ตรงกับใบเสร็จรับเงินจริง';

    public function handle(GlPostingService $glPosting): int
    {
        $receiptNo = (string) $this->argument('receipt_no');
        $discountAmount = round((float) $this->argument('discount_amount'), 2);
        $reason = (string) $this->option('reason');

        if ($discountAmount <= 0) {
            $this->error('ยอดส่วนลดต้องมากกว่า 0');
            return 1;
        }

        $receipt = PosReceipt::where('receipt_no', $receiptNo)->first();
        if (! $receipt) {
            $this->error("ไม่พบบิล {$receiptNo}");
            return 1;
        }

        DB::transaction(function () use ($receipt, $discountAmount, $reason, $glPosting) {
            $gross = (float) $receipt->gross_sales;
            $newNet = max(0.0, round($gross - $discountAmount, 2));

            $oldValues = [
                'discount_amount' => (float) $receipt->discount_amount,
                'net_sales' => (float) $receipt->net_sales,
            ];

            $receipt->update([
                'discount_amount' => $discountAmount,
                'net_sales' => $newNet,
            ]);

            PosReceiptDiscount::updateOrCreate(
                ['pos_receipt_id' => $receipt->id, 'discount_type' => 'manual'],
                ['amount' => $discountAmount]
            );

            if ($receipt->document_id) {
                $doc = Document::find($receipt->document_id);
                if ($doc) {
                    $doc->update([
                        'discount_amount' => $discountAmount,
                        'total_amount' => $newNet,
                        'remark' => trim(($doc->remark ? $doc->remark . ' | ' : '') . $reason),
                    ]);
                    // Re-post GL for document
                    try {
                        $glPosting->reverseDocument($doc, 'Reconcile discount: ' . $reason);
                        $glPosting->postSaleDocument($doc);
                    } catch (\Throwable $e) {
                        // ignore GL re-post if not configured
                    }
                }
            }

            // Recalculate shift totals if shift is open
            if ($receipt->shift) {
                $totals = DB::table('pos_receipts')
                    ->where('pos_shift_id', $receipt->shift->id)
                    ->where('status', 'completed')
                    ->selectRaw('coalesce(sum(net_sales),0) as net_sales')
                    ->first();
                $receipt->shift->update([
                    'cash_sales' => (float) ($totals->net_sales ?? 0),
                ]);
            }

            DB::table('audit_logs')->insert([
                'user_id' => null,
                'branch_id' => $receipt->terminal?->branch_id ?? $receipt->shift?->branch_id,
                'action' => 'reconcile_discount',
                'table_name' => 'pos_receipts',
                'record_id' => $receipt->id,
                'old_values' => json_encode($oldValues, JSON_UNESCAPED_UNICODE),
                'new_values' => json_encode(['discount_amount' => $discountAmount, 'net_sales' => $newNet], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
            ]);
        });

        $this->info("ปรับปรุงบิล {$receiptNo} เรียบร้อย: ส่วนลด = {$discountAmount}, ยอดสุทธิ = " . ($receipt->fresh()->net_sales));
        return 0;
    }
}
