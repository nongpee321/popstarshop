<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'pos_terminal_id', 'pos_shift_id', 'document_id', 'receipt_no', 'receipt_date', 'cashier_id',
    'cashier_salesman_id', 'member_id', 'gross_sales', 'discount_amount',
    'vat_amount', 'net_sales', 'status', 'voided_at', 'voided_by', 'void_reason',
])]
class PosReceipt extends Model
{
    const UPDATED_AT = null;

    public function getDiscountAmountAttribute($value): float
    {
        $discount = (float) $value;
        $gross = (float) ($this->attributes['gross_sales'] ?? 0);
        $net = (float) ($this->attributes['net_sales'] ?? 0);
        if ($discount <= 0 && $gross > $net + 0.01) {
            return max(0.0, round($gross - $net, 2));
        }
        return $discount;
    }

    public function getNetSalesAttribute($value): float
    {
        $net = (float) $value;
        $gross = (float) ($this->attributes['gross_sales'] ?? 0);
        $discount = (float) ($this->attributes['discount_amount'] ?? 0);
        if ($discount > 0 && abs($net - $gross) < 0.01) {
            return max(0.0, round($gross - $discount, 2));
        }
        return $net;
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'pos_terminal_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class, 'pos_shift_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function cashierSalesman(): BelongsTo
    {
        return $this->belongsTo(Salesman::class, 'cashier_salesman_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosReceiptItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosPayment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(PosReceiptDiscount::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(PosCoupon::class);
    }

    protected function casts(): array
    {
        return [
            'receipt_date' => 'datetime',
            'voided_at' => 'datetime',
            'gross_sales' => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'vat_amount' => 'decimal:4',
            'net_sales' => 'decimal:4',
        ];
    }
}
