<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cost',
        'estimated_days',
        'is_active',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
