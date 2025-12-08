<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('payment-method.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment-method.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ]);
        PaymentMethod::create($request->only('name', 'description', 'is_active'));
        return redirect()->route('dashboard.payment-method.index')->with('success', 'Payment method created.');
    }
    
    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('payment-method.edit', compact('paymentMethod'));
    }
    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ]);
        $paymentMethod->update($request->only('name', 'description', 'is_active'));
        return redirect()->route('dashboard.payment-method.index')->with('success', 'Payment method updated.');
    }
    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();
        return redirect()->route('dashboard.payment-method.index')->with('success', 'Payment method deleted.');
    }
}
