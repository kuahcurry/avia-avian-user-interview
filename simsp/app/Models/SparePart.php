<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model
{
    public $timestamps = false;
    protected $table = 'spare_parts';
    protected $fillable =
    [
        'current_stock',
    ];

    public function sparePartUsage(): HasMany
    {
        return $this->hasMany(SparePartUsage::class);
    }

    public function stockHistory(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }
}
