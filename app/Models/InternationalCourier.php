<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalCourier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function consolidations()
    {
        return $this->hasMany(Consolidation::class);
    }
}
