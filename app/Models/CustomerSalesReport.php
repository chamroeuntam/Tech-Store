<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_report_id',
        'user_id',
        'total_orders',
        'total_spent',
        'average_order_value',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'average_order_value' => 'decimal:2',
    ];

    public function salesReport()
    {
        return $this->belongsTo(SalesReport::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
