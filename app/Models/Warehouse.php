<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['branch_id', 'code', 'name'])]
class Warehouse extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::addGlobalScope('active_branch', function (Builder $builder) {
            $builder->where(function ($q) {
                $q->whereNull('warehouses.branch_id')
                  ->orWhereHas('branch'); // Branch model has global scope 'active'
            });
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(WarehouseLocation::class);
    }
}
