<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['productName', 'productDescription', 'price', 'category_id'];

    //A Product belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
