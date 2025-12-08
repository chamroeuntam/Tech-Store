<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'order_id',
        'payment_method',
        'status',
        'amount',
        'gateway_response',
        'completed_at',
        'invoice_id',
        'qr_string',
        'qr_image',
        'expires_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
