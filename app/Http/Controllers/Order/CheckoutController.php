<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;

class CheckoutController extends Controller
{
    protected function getCartIdentifier(Request $request)
    {
        if (Auth::check()) {
            return ['user_id', Auth::id()];
        }
        return ['session_id', $request->session()->getId()];
    }

    // Show checkout page
    public function index(Request $request){
        // Check for 'buy now' (single product checkout)
        $singleProductId = $request->input('product_id') ?? $request->input('product');
        if ($singleProductId) {
            $product = Product::find($singleProductId);
            $quantity = max(1, (int)$request->input('quantity', 1));
            $cartItems = collect([
                (object)[
                    'id' => null,
                    'user_id' => Auth::id(),
                    'session_id' => $request->session()->getId(),
                    'product_id' => $product ? $product->id : null,
                    'quantity' => $quantity,
                    'product' => $product,
                ]
            ]);
        } else {
            if (Auth::check()) {
                $cartItems = Cart::where(function($query) use ($request) {
                    $query->where('user_id', Auth::id())
                          ->orWhere('session_id', $request->session()->getId());
                })->with('product')->get();
            } else {
                $cartItems = Cart::where('session_id', $request->session()->getId())
                    ->with('product')->get();
            }
        }
        
        $products = $cartItems->map(function ($item) {
            $prod = $item->product;
            return [
                'id' => $item->id,
                'product_id' => $prod->id ?? null,
                'name' => $prod->name ?? 'Unknown',
                'category' => $prod->category->name ?? 'Uncategorized',
                'stock' => $prod->quantity ?? 0,
                'price' => (float)($prod->price ?? 0),
                'quantity' => (int)$item->quantity,
                'image_url' => $prod->image_url ?? null,
            ];
        });
        

        $shipping_methods = ShippingMethod::all();
        $payment_methods = PaymentMethod::all();

        $warning = null;
        if ($cartItems->count() == 0) {
            $warning = 'Your cart is empty. Please add products before checking out.';
        }

        return view(
            'checkout.index',
            [
                'cartItems' => $cartItems,
                'products' => $products,
                'user' => Auth::user(),
                'shipping_methods' => $shipping_methods,
                'payment_methods' => $payment_methods,
                'warning' => $warning,
            ]
        );
    }

    // Handle checkout submit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'           => 'required|string|max:255',
            'last_name'            => 'required|string|max:255',
            'phone'                => 'required|string|max:20',
            'email'                => 'required|email|max:255',
            'shipping_first_name'  => 'required|string|max:255',
            'shipping_last_name'   => 'required|string|max:255',
            'shipping_phone'       => 'required|string|max:20',
            'shipping_address'     => 'required|string|max:255',
            'shipping_method'      => 'required|exists:shipping_methods,id',
            'payment_method'       => 'required|string',
            'order_notes'          => 'nullable|string|max:1000',
        ]);

        // Support Buy Now and Cart checkout
        $singleProductId = $request->input('product_id') ?? $request->input('product');
        if ($singleProductId) {
            $product = Product::find($singleProductId);
            $quantity = max(1, (int)$request->input('quantity', 1));
            if (!$product) {
                return redirect()->back()->with('error', 'Product not found.');
            }
            $cartItems = collect([
                (object)[
                    'id' => null,
                    'user_id' => Auth::id(),
                    'session_id' => $request->session()->getId(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'product' => $product,
                ]
            ]);
        } else {
            [$key, $value] = $this->getCartIdentifier($request);
            $cartItems = Cart::where($key, $value)->with('product')->get();
            if ($cartItems->count() == 0) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }
        }

        try {
            // Calculate total using product price
            $total = 0;
            foreach ($cartItems as $item) {
                if ($item->product) {
                    $total += $item->product->price * $item->quantity;
                }
            }

            // Create order
            $order = Order::create([
                'user_id'              => Auth::id(),
                'first_name'           => $validated['first_name'],
                'last_name'            => $validated['last_name'],
                'phone'                => $validated['phone'],
                'email'                => $validated['email'],

                'shipping_first_name'  => $validated['shipping_first_name'],
                'shipping_last_name'   => $validated['shipping_last_name'],
                'shipping_phone'       => $validated['shipping_phone'],
                'shipping_address'     => $validated['shipping_address'],
                'shipping_method_id'   => $validated['shipping_method'],

                'payment_method'       => $validated['payment_method'],
                'order_notes'          => $validated['order_notes'] ?? null,

                'total_amount'         => $total,
                'status'               => 'pending',
            ]);

            // Save each item to OrderItems
            foreach ($cartItems as $item) {
                if ($item->product) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->product->price,
                    ]);
                }
            }

            // Clear cart after checkout
            Cart::where($key, $value)->delete();

            // Notify staff and admin
            // Eager load order_items and product for notification
            $order->load(['order_items.product', 'shippingMethod']);

            $recipients = User::whereIn('role', ['admin', 'staff'])->get();
            foreach ($recipients as $recipient) {
                $recipient->notify(new OrderPlacedNotification($order));
            }
            // Notify customer
            $customer = User::find($order->user_id);
            if ($customer) {
                $customer->notify(new OrderPlacedNotification($order));
            }

            Cart::where($key, $value)->delete();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Throwable $e) {
            Log::error('Order placement failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Order failed. Please contact support.');
        }
    }
}
