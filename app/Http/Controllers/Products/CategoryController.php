<?php
namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        $categories = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('category.index', compact('categories'));
    }

    public function create(){
        return view('category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories,name|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()->route('dashboard.categories.index')->with('success', 'Category created successfully.');
    }


    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('category.edit', compact('category'));      
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id . '|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('dashboard.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('dashboard.categories.index')->with('success', 'Category deleted successfully.');
    }
}
