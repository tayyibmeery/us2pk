<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'shipment_id',
        'date',
        'amount_due',
        'cod',
        'cod_date',
        'output_tax'
    ];

    protected $casts = [
        'date' => 'date',
        'cod_date' => 'date',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
