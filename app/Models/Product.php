<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    // Ensure image_url is included in JSON responses
    protected $appends = ['image_url'];
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category_id',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (Str::startsWith($this->image, 'storage/')) {
            return asset($this->image);
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }
}
