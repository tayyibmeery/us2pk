<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightDiscount extends Model
{
    protected $fillable = ['warehouse', 'discount_percent'];
}
