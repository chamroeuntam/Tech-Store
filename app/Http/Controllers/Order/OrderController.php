<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;

use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Helpers\KHQRData;

class OrderController extends Controller
{
    // Show order detail by numeric ID
    public function showById($id)
    {
        $order = Order::with(['order_items.product', 'payment'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        return view('order.order-detail', compact('order'));
    }
    // ================================
    // USER ORDER DETAIL PAGE
    // ================================
    public function show($order_id)
    {
        $order = Order::with(['order_items.product', 'payment'])
            ->where('order_id', $order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('order.order-detail', compact('order'));
    }

    // ================================
    // ADMIN DASHBOARD PAGE
    // ================================
    public function adminDashboard()
    {
        $total_orders     = Order::count();
        $pending_orders   = Order::where('status', 'pending')->count();
        $confirmed_orders = Order::where('status', 'confirmed')->count();
        $completed_orders = Order::where('status', 'completed')->count();
        $cancelled_orders = Order::where('status', 'cancelled')->count();

        return view('order.Admin.dashboard', compact(
            'total_orders',
            'pending_orders',
            'confirmed_orders',
            'completed_orders',
            'cancelled_orders'
        ));
    }

    // ================================
    // USER ORDER LIST PAGE
    // ================================
    public function index()
    {
        $orders = Order::with(['order_items.product', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('order.order-list', compact('or ders'));
    }

    // ================================
    // SHOW CHECKOUT PAGE
    // ================================
    public function checkoutPage(Request $request)
    {
        $productId   = $request->input('product');
        $quantity    = max(1, (int)$request->input('quantity', 1));
        $sessionCart = session('cart', []);

        // ----------------------------
        // BUY NOW: add single product to cart (normalize to numeric list)
        // ----------------------------
        if ($productId) {
            $product = Product::find($productId);

            if ($product && $quantity > 0) {
                // Always clear session cart before Buy Now
                session()->forget('cart');
                $newItem = [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'price'      => (float)$product->price,
                    'quantity'   => $quantity,
                    'image'      => $product->image ?? null,
                ];
                session(['cart' => [$newItem]]);
                $sessionCart = [$newItem];
            } else {
                return redirect()->back()->with('error', 'Invalid product or quantity.');
            }
        }

        // If still empty
        if (empty($sessionCart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        $shipping_methods = ShippingMethod::all();
        $payment_methods  = PaymentMethod::all();
        $selectedPaymentMethod = old('payment_method') ?? null;
        $user = Auth::user();

        // Normalize cart for display (supports many shapes)
        $normalized = $this->normalizeSessionCart($sessionCart);

        $cartItems = [];
        $subtotal  = 0;

        foreach ($normalized as $entry) {
            $price = (float)($entry['price'] ?? 0);
            $qty   = $this->extractQuantity($entry);
            $itemTotal = $price * $qty;

            $cartItems[] = (object)[
                'product_id'  => $entry['product_id'] ?? null,
                'name'        => $entry['name'] ?? null,
                'price'       => $price,
                'quantity'    => $qty,
                'total_price' => $itemTotal,
                'image'       => $entry['image'] ?? null,
            ];

            $subtotal += $itemTotal;
        }

        $cart = (object)[
            'items'       => $cartItems,
            'total_price' => $subtotal,
        ];

        // Generate Bakong QR
        $qrImage = null;

        try {
            // Use PaymentController logic for KHQR QR string and image
            if ($user && $subtotal > 0) {
                $merchant = new \KHQR\Models\MerchantInfo(
                    'chamroeun_tam@wing',
                    'Chamroeun Tam',
                    'PHNOM PENH',
                    'MID001',
                    'Dev Bank',
                    null,
                    \KHQR\Helpers\KHQRData::CURRENCY_KHR,
                    round($subtotal, 2)
                );
                $result = \KHQR\BakongKHQR::generateMerchant($merchant);
                if (isset($result->data['qr'])) {
                    $qrString = $result->data['qr'];
                    $qrCode = new \Endroid\QrCode\QrCode($qrString);
                    $writer = new \Endroid\QrCode\Writer\PngWriter();
                    $qrImage = base64_encode($writer->write($qrCode)->getString());
                }
            }
        } catch (\Throwable $e) {
            Log::error('KHQR generation failed: ' . $e->getMessage());
        }

        return view('checkout.index', [
            'cart'                  => $cart,
            'user'                  => $user,
            'shipping_methods'      => $shipping_methods,
            'payment_methods'       => $payment_methods,
            'selectedPaymentMethod' => $selectedPaymentMethod,
            'qrCode'                => $qrImage,
        ]);
    }

    // ================================
    // PROCESS CHECKOUT
    // ================================
    public function checkout(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:50',
            'shipping_address' => 'required|string|max:1000',
            'payment_method'   => 'required',
        ]);

        try {
            DB::beginTransaction();

            $sessionCart = session('cart', []);
            $cartItems = [];
            $subtotal  = 0;

            // If session cart empty, support Buy Now via product/quantity in request
            if (empty($sessionCart)) {
                $productId = $request->input('product');
                $quantity = max(1, (int)$request->input('quantity', 1));
                if ($productId) {
                    $product = \App\Models\Product::find($productId);
                    if ($product && $quantity > 0) {
                        $price = (float)$product->price;
                        $itemTotal = $price * $quantity;
                        $cartItems[] = [
                            'product_id'  => $product->id,
                            'price'       => $price,
                            'quantity'    => $quantity,
                            'total_price' => $itemTotal,
                        ];
                        $subtotal += $itemTotal;
                    } else {
                        return redirect()->back()->with('error', 'Invalid product or quantity.');
                    }
                } else {
                    // Fallback to DB cart
                    $cartQuery = \App\Models\Cart::query();
                    if (Auth::check()) {
                        $cartQuery->where('user_id', Auth::id());
                    } else {
                        $cartQuery->where('session_id', $request->session()->getId());
                    }
                    $dbCartItems = $cartQuery->with('product')->get();
                    if ($dbCartItems->isEmpty()) {
                        return redirect()->back()->with('error', 'Cart is empty.');
                    }
                    foreach ($dbCartItems as $item) {
                        $price = (float)($item->product->price ?? 0);
                        $qty = (int)$item->quantity;
                        $itemTotal = $price * $qty;
                        $cartItems[] = [
                            'product_id'  => $item->product_id,
                            'price'       => $price,
                            'quantity'    => $qty,
                            'total_price' => $itemTotal,
                        ];
                        $subtotal += $itemTotal;
                    }
                }
            } else {
                foreach ($sessionCart as $entry) {
                    $price = (float)($entry['price'] ?? 0);
                    $qty = $this->extractQuantity($entry);
                    $itemTotal = $price * $qty;
                    $cartItems[] = [
                        'product_id'  => is_array($entry) ? $entry['product_id'] : $entry->product_id,
                        'price'       => $price,
                        'quantity'    => $qty,
                        'total_price' => $itemTotal,
                    ];
                    $subtotal += $itemTotal;
                }
            }


            // Calculate shipping cost and total
            $shippingCost = 0;
            $shippingMethod = null;
            if ($request->filled('shipping_method')) {
                $shippingMethod = ShippingMethod::find($request->shipping_method);
                if ($shippingMethod) {
                    $shippingCost = (float)$shippingMethod->cost;
                }
            }
            $total = $subtotal + $shippingCost;

            $order = Order::create([
                'order_id'           => \Illuminate\Support\Str::uuid(),
                'user_id'            => Auth::id(),
                'status'             => 'pending',
                'first_name'         => $request->first_name,
                'last_name'          => $request->last_name,
                'phone'              => $request->phone,
                'email'              => $request->email ?? null,
                'shipping_first_name'=> $request->shipping_first_name ?? null,
                'shipping_last_name' => $request->shipping_last_name ?? null,
                'shipping_phone'     => $request->shipping_phone ?? null,
                'shipping_address'   => $request->shipping_address,
                'shipping_method_id' => $request->shipping_method,
                'payment_method_id'  => $request->payment_method,
                'total_amount'       => $total,
                'shipping_cost'      => $shippingCost,
                'order_notes'        => $request->order_notes,
            ]);

            // INSERT ORDER ITEMS & ADJUST STOCK
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['total_price'],
                ]);

                if ($item['product_id']) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->quantity = max(0, $product->quantity - $item['quantity']);
                        $product->save();
                    }
                }
            }

            // CREATE PAYMENT
            $paymentMethod = PaymentMethod::find($request->payment_method);

            $payment = Payment::create([
                 'order_id'       => $order->id,
                 'payment_method' => $request->payment_method,
                 'amount'         => $total,
                 'status'         => 'pending',
                 'name'           => $paymentMethod?->name ?? 'Unknown',
                 'description'    => $paymentMethod?->description,
                 'is_active'      => $paymentMethod?->is_active ?? true,
                 // Always set invoice_id for all payments
                 'invoice_id'     => \Illuminate\Support\Str::uuid()->toString(),
                 // If KHQR, set qr_string and qr_image
                 'qr_string'      => isset($qrString) ? $qrString : null,
                 'qr_image'       => isset($qrImage) ? $qrImage : null,
            ]);

            if (Schema::hasColumn('orders', 'payment_id')) {
                $order->update(['payment_id' => $payment->id]);
            }

            DB::commit();

            // Clear session cart
            session()->forget('cart');
            // Clear database cart for user/session
            if (Auth::check()) {
                \App\Models\Cart::where('user_id', Auth::id())->delete();
            } else {
                \App\Models\Cart::where('session_id', $request->session()->getId())->delete();
            }

            // If KHQR payment method selected, redirect to KHQR payment flow
            if (strtolower($paymentMethod?->name) === 'khqr') {
                // Pass order info to KHQR payment controller, including order_id and payment_method
                return redirect()->route('pay.create', [
                    'amount' => $order->total_amount,
                    'currency' => 'KHR',
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                ]);
            }

            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Order failed.');
        }
    }

    // ================================
    // ADMIN ORDER DETAILS
    // ================================
    public function adminOrderDetails($orderId)
    {
        $order = Order::with(['order_items.product', 'payment', 'shippingMethod', 'user', 'paymentMethod'])
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('order.Admin.order-detail', compact('order'));
    }


    // Helper: normalize session cart into numeric list of arrays
    protected function normalizeSessionCart($sessionCart): array
    {
        $result = [];

        // If it's an Eloquent collection or object with ->toArray
        if (is_object($sessionCart) && method_exists($sessionCart, 'toArray')) {
            $sessionCart = $sessionCart->toArray();
        }

        // If numeric-list already
        if ($this->isList($sessionCart)) {
            foreach ($sessionCart as $entry) {
                if (is_object($entry)) {
                    $entry = (array)$entry;
                }
                $result[] = $entry;
            }
            return $result;
        }

        // If associative keyed by product id
        if (is_array($sessionCart)) {
            foreach ($sessionCart as $key => $entry) {
                if (is_object($entry)) {
                    $entry = (array)$entry;
                }
                // If the entry doesn't have product_id, attempt to set from key
                if (!isset($entry['product_id']) && is_scalar($key)) {
                    $entry['product_id'] = $key;
                }
                $result[] = $entry;
            }
        }

        return $result;
    }

    // ----------------------------
    // Helper: extract quantity reliably
    // Accepts keys 'quantity' or 'qty' or object property, defaults to 1
    // ----------------------------
    protected function extractQuantity($entry): int
    {
        if (is_object($entry)) {
            $entry = (array)$entry;
        }
        $qty = 1;
        if (isset($entry['quantity'])) {
            $qty = (int)$entry['quantity'];
        } elseif (isset($entry['qty'])) {
            $qty = (int)$entry['qty'];
        } elseif (isset($entry['amount'])) { // sometimes frontends use 'amount'
            $qty = (int)$entry['amount'];
        } elseif (isset($entry[ 'quantity' ])) {
            $qty = (int)$entry['quantity'];
        }
        return max(1, $qty);
    }

    // ----------------------------
    // Helper: determine if array is list (numeric keys 0..n-1)
    // ----------------------------
    protected function isList($arr): bool
    {
        if (!is_array($arr)) return false;
        $i = 0;
        foreach ($arr as $k => $_) {
            if ($k !== $i) return false;
            $i++;
        }
        return true;
    }
}
