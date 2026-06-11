<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubCategory extends Model
{
    protected $table = 'sub_sub_categories';

    protected $fillable = ['name', 'description', 'category_id', 'sub_category_id', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
