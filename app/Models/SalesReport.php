<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'start_date',
        'end_date',
        'total_orders',
        'total_revenue',
        'total_items_sold',
        'average_order_value',
        'generated_by',
        'is_cached',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'is_cached' => 'boolean',
    ];

    // ✅ Relations
    public function productSales()
    {
        return $this->hasMany(ProductSalesReport::class);
    }

    public function customerSales()
    {
        return $this->hasMany(CustomerSalesReport::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
