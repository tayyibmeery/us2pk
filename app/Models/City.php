<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['city_name', 'city_code', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
