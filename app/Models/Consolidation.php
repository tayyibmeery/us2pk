<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consolidation extends Model
{
    protected $fillable = [
        'consol_id',
        'awb_number',
        'warehouse_id',
        'date_dispatched',
        'total_us2pk_charges',
        'direct_costs',
        'total_weight_kg'
    ];

    protected $casts = [
        'date_dispatched' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
