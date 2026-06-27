<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightDiscount extends Model
{
    protected $fillable = ['warehouse_id', 'discount_percent'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
