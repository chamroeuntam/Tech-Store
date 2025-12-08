<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_report_id',
        'product_id',
        'quantity_sold',
        'total_revenue',
        'average_price',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'average_price' => 'decimal:2',
    ];

    public function salesReport()
    {
        return $this->belongsTo(SalesReport::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
