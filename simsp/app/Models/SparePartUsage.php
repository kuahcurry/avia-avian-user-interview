<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class SparePartUsage extends Model
{
    public $timestamps = false;

    protected $table = 'spare_part_usage'; 
    protected $fillable =
    [
        'machine_id',
        'spare_part_id',
        'quantity_used',
        'usage_date',
        'notes',
        'recorded_by',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
