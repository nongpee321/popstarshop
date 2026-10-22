<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'code', 'name', 'promotion_type', 'starts_at', 'ends_at',
    'start_time', 'end_time', 'days_of_week',
    'min_qty', 'min_amount',
    'discount_amount', 'discount_percent', 'product_id', 'branch_ids', 'note', 'is_active',
])]
class Promotion extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function appliesToBranch(?int $branchId): bool
    {
        if (empty($this->branch_ids)) {
            return true;
        }

        if (! $branchId) {
            return true;
        }

        return in_array($branchId, array_map('intval', (array) $this->branch_ids), true);
    }

    public function isRunningAt(Carbon $now, ?int $branchId = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($branchId && ! $this->appliesToBranch($branchId)) {
            return false;
        }

        $today = $now->toDateString();

        if ($this->starts_at && $today < $this->starts_at->toDateString()) {
            return false;
        }

        if ($this->ends_at && $today > $this->ends_at->toDateString()) {
            return false;
        }

        $currentTime = $now->format('H:i:s');

        if ($this->start_time) {
            $startTimeStr = is_string($this->start_time) ? substr($this->start_time, 0, 8) : $this->start_time->format('H:i:s');
            if ($currentTime < $startTimeStr) {
                return false;
            }
        }

        if ($this->end_time) {
            $endTimeStr = is_string($this->end_time) ? substr($this->end_time, 0, 8) : $this->end_time->format('H:i:s');
            if ($currentTime > $endTimeStr) {
                return false;
            }
        }

        if (! empty($this->days_of_week)) {
            $allowedDays = is_array($this->days_of_week) 
                ? $this->days_of_week 
                : explode(',', (string) $this->days_of_week);
            if (! in_array((string) $now->dayOfWeek, $allowedDays, true)) {
                return false;
            }
        }

        return true;
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'branch_ids' => 'array',
            'min_qty' => 'decimal:8',
            'min_amount' => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'discount_percent' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }
}
