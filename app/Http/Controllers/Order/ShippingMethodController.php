<?php


namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{

    // 1. List all shipping methods
    public function index()
    {
        $methods = ShippingMethod::orderBy('created_at', 'desc')->get();
        return view('shipping.index', compact('methods'));
    }

    // 2. Show create form
    public function create()
    {
        return view('shipping.create');
    }

    // 3. Store new shipping method
   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        ShippingMethod::create([
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'estimated_days' => $request->estimated_days,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('dashboard.shipping.index')->with('success', 'Shipping method created successfully.');
    }

   
    // 4. Show edit form
  
    public function edit($id)
    {
        $method = ShippingMethod::findOrFail($id);
        return view('shipping.edit', compact('method'));
    }

   
    // 5. Update shipping method

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $method = ShippingMethod::findOrFail($id);

        $method->update([
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'estimated_days' => $request->estimated_days,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('dashboard.shipping.index')->with('success', 'Shipping method updated successfully.');
    }


    // 6. Delete shipping method

    public function destroy($id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->delete();

        return redirect()->route('dashboard.shipping.index')->with('success', 'Shipping method deleted successfully.');
    }
}
