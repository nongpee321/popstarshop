<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['warehouse_id', 'code', 'name'])]
class WarehouseLocation extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::addGlobalScope('active_warehouse', function (Builder $builder) {
            $builder->where(function ($q) {
                $q->whereNull('warehouse_locations.warehouse_id')
                  ->orWhereHas('warehouse'); // Warehouse model has active_branch scope
            });
        });
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
