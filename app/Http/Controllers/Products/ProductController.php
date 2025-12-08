<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Slider;

class ProductController extends Controller
{
    // ✅ AJAX FILTER (NO RELOAD)
    public function ajaxFilter(Request $request)
    {
        Log::info('AJAX Filter Request', $request->all());

        $query = Product::with('category')->where('quantity', '>', 0);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm);
            });
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->get();
        // Ensure category relation is always included in the JSON response
        $products->each(function ($product) {
            $product->setRelation('category', $product->category);
        });
        return response()->json([
            'products' => $products
        ]);
    }


    // ✅ PRODUCT LIST PAGE
    public function index(Request $request) 
    {
        $query = Product::with('category')->where('quantity', '>', 0);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm);
            });
        }

        $products = $query->get();
        $categories = Category::all();
        $sliders = Slider::get();

        return view('products.index', compact('products', 'categories', 'sliders'));
    }


    public function show(Product $product)
    {
        $product->load('category');
        return view('products.detail', compact('product'));
    }


    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:1',
            'quantity'    => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('dashboard.home')
            ->with('success', 'Product created successfully.');
    }


    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }


    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('dashboard.home')
            ->with('success', 'Product updated successfully!');
    }


    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('dashboard.home')
            ->with('success', 'Product deleted successfully.');
    }
}
