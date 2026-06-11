<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PcodeCounter extends Model
{
    protected $fillable = ['city_id', 'last_number'];
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
