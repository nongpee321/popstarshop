<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Explicitly heal bill CSB00120261031003 (Gross 650, Discount 100, Net 550)
        $targetBill = DB::table('pos_receipts')->where('receipt_no', 'CSB00120261031003')->first();
        if ($targetBill) {
            DB::table('pos_receipts')
                ->where('id', $targetBill->id)
                ->update([
                    'gross_sales' => 650.00,
                    'discount_amount' => 100.00,
                    'net_sales' => 550.00,
                ]);

            if ($targetBill->document_id) {
                DB::table('documents')
                    ->where('id', $targetBill->document_id)
                    ->update([
                        'total_amount' => 550.00,
                    ]);
            }
        }

        // 2. Heal past receipts where gross_sales > net_sales but discount_amount was recorded as 0
        DB::table('pos_receipts')
            ->where('discount_amount', '<=', 0)
            ->whereRaw('gross_sales > net_sales')
            ->update([
                'discount_amount' => DB::raw('gross_sales - net_sales'),
            ]);

        // 3. Heal past receipts where discount_amount > 0 but net_sales was left equal to gross_sales
        DB::table('pos_receipts')
            ->where('discount_amount', '>', 0)
            ->whereRaw('ABS(net_sales - gross_sales) < 0.01')
            ->update([
                'net_sales' => DB::raw('gross_sales - discount_amount'),
            ]);

        // 4. Heal receipts where payments total is less than gross_sales and discount_amount is 0
        $receiptsWithPayments = DB::table('pos_receipts as r')
            ->join('pos_payments as p', 'p.pos_receipt_id', '=', 'r.id')
            ->where('r.discount_amount', '<=', 0)
            ->select('r.id', 'r.gross_sales', 'r.document_id', DB::raw('SUM(p.amount) as paid_sum'))
            ->groupBy('r.id', 'r.gross_sales', 'r.document_id')
            ->havingRaw('r.gross_sales > SUM(p.amount) + 0.01')
            ->get();

        foreach ($receiptsWithPayments as $row) {
            $paid = round((float) $row->paid_sum, 2);
            $gross = round((float) $row->gross_sales, 2);
            $diff = round($gross - $paid, 2);
            if ($diff > 0) {
                DB::table('pos_receipts')
                    ->where('id', $row->id)
                    ->update([
                        'discount_amount' => $diff,
                        'net_sales' => $paid,
                    ]);

                if ($row->document_id) {
                    DB::table('documents')
                        ->where('id', $row->document_id)
                        ->update([
                            'total_amount' => $paid,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        // No need to revert corrected discount values
    }
};
