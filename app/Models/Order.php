<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'status',

        // Billing Info
        'first_name',
        'last_name',
        'email',
        'phone',

        // Shipping Info
        'shipping_first_name',
        'shipping_last_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address',
        'shipping_method_id',           

        // Payment Method
        'payment_method_id',

        // Amounts
        'total_amount',
        'shipping_cost',

        'order_notes',
    ];

    protected static function boot()
    {
        parent::boot();

        // auto-create short unique order_id
        static::creating(function ($order) {
            $order->order_id = strtoupper(Str::random(8));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_amount;
    }


    public function getTotalItemsAttribute()
    {
        return $this->order_items ? $this->order_items->sum('quantity') : 0;
    }
      public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Subtotal: sum of product prices (quantity * unit price for each item)
    public function getSubtotalAttribute()
    {
        return $this->order_items ? $this->order_items->sum(function ($item) {
            return $item->quantity * $item->price;
        }) : 0;
    }

    // Total: subtotal + shipping
    public function getTotalAttribute()
    {
        return $this->subtotal + ($this->shipping_cost ?? 0);
    }
    
}
