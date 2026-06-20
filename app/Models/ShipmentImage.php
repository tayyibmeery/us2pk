<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentImage extends Model
{
    protected $fillable = ['shipment_id', 'image_path'];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
