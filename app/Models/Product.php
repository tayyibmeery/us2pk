<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'store_id', 'url', 'price', 'description', 'status'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
