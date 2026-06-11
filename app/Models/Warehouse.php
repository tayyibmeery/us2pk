<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'code'];

    public function consolidations()
    {
        return $this->hasMany(Consolidation::class);
    }
}
