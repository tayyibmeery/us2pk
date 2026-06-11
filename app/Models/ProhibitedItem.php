<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProhibitedItem extends Model
{
    protected $fillable = ['item_name', 'description'];
}
