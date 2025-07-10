<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'image_path'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
