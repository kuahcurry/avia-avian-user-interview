<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    public $timestamps = false;
    public $table = 'stock_history';

    public $fillable =
    [
        'spare_part_id',
        'quantity_changed',
        'old_stock',
        'new_stock',
        'reason',
        'changed_by',
    ];

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
