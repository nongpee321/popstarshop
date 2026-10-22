<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code', 'name', 'discount_type', 'discount_value',
    'min_purchase_amount', 'max_discount_amount',
    'starts_date', 'ends_date', 'usage_limit', 'times_used', 'is_active',
])]
class Coupon extends Model
{
    public function isValidFor(float $subtotal): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'คูปองนี้ถูกปิดใช้งานแล้ว'];
        }

        $today = now()->toDateString();
        if ($this->starts_date && $today < $this->starts_date->toDateString()) {
            return ['valid' => false, 'message' => 'คูปองยังไม่เริ่มใช้งาน (เริ่มวันที่ '.$this->starts_date->format('d/m/Y').')'];
        }

        if ($this->ends_date && $today > $this->ends_date->toDateString()) {
            return ['valid' => false, 'message' => 'คูปองหมดอายุแล้ว (หมดอายุวันที่ '.$this->ends_date->format('d/m/Y').')'];
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'คูปองนี้ถูกใช้งานจนครบโควตาแล้ว'];
        }

        if ((float) $this->min_purchase_amount > 0 && $subtotal < (float) $this->min_purchase_amount) {
            return ['valid' => false, 'message' => 'ยอดซื้อไม่ถึงขั้นต่ำ ฿'.number_format((float) $this->min_purchase_amount, 2)];
        }

        $discount = 0.0;
        if ($this->discount_type === 'percent') {
            $discount = round($subtotal * ((float) $this->discount_value / 100), 2);
            if ($this->max_discount_amount !== null && (float) $this->max_discount_amount > 0) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
        } else {
            $discount = (float) $this->discount_value;
        }

        $discount = min($discount, $subtotal);

        return [
            'valid' => true,
            'discount' => $discount,
            'code' => $this->code,
            'name' => $this->name,
            'message' => 'ใช้คูปองสำเร็จ ลด ฿'.number_format($discount, 2),
        ];
    }

    protected function casts(): array
    {
        return [
            'starts_date' => 'date',
            'ends_date' => 'date',
            'discount_value' => 'decimal:4',
            'min_purchase_amount' => 'decimal:4',
            'max_discount_amount' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }
}
