<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    public $timestamps = false;
    protected $table = 'machines'; 
    
    public function sparePartUsage(): HasMany
    {
        return $this->hasMany(SparePartUsage::class);
    }
}
