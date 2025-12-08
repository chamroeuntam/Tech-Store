<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        // ...existing code...
        $totalProducts = Product::count();
        $inStock = Product::where('quantity', '>', 5)->count();
        $lowStock = Product::whereBetween('quantity', [1, 5])->count();
        $outOfStock = Product::where('quantity', 0)->count();

        $stats = [
            'total' => $totalProducts,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ];

        $products = Product::orderBy('quantity', 'asc')->get();

        $actionRoutes = [
            'addProduct' => 'products.create',
            'addCategory' => 'categories.create', 
            'manageProducts' => 'products.index',
        ];

        return view('products.dashboard', compact('stats', 'products', 'actionRoutes'));
    }
}
