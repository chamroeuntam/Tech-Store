<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function getCartIdentifier(Request $request)
    {
        if (Auth::check()) {
            return ['user_id', Auth::id()];
        }
        return ['session_id', $request->session()->getId()];
    }

    // show cart
    public function index(Request $request)
    {
        [$key, $value] = $this->getCartIdentifier($request);
        $items = Cart::where($key, $value)->with('product')->get();
        $products = $items->map(function ($item) {
            $prod = $item->product;
            return [
                'id' => $item->id,
                'product_id' => $prod->id,
                'name' => $prod->name ?? 'Unknown',
                'category' => $prod->category->name ?? 'Uncategorized',
                'stock' => $prod->quantity ?? 0,
                'price' => (float)$prod->price,
                'quantity' => (int)$item->quantity,
                'image_url' => $prod->image_url ?? null,
            ];
        });

        $subtotal = $items->sum(fn($i) => $i->quantity * ($i->product->price ?? 0));
        $total = $subtotal;

        return view('cart.index', compact('items', 'products', 'subtotal', 'total'));
    }

    // add product to cart
    public function add(Request $request, $productId)
    {
        [$key, $value] = $this->getCartIdentifier($request);

        $product = Product::find($productId);
        if (!$product || ($product->quantity ?? 0) < 1) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Product unavailable or out of stock'], 404)
                : back()->with('error', 'Product unavailable or out of stock');
        }

        $request->validate(['quantity' => 'nullable|integer|min:1']);
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity > $product->quantity) $quantity = $product->quantity;

        // Always use user_id and session_id if logged in
        $query = Cart::query();
        if (Auth::check()) {
            $query->where('user_id', Auth::id())
                  ->where('session_id', $request->session()->getId());
        } else {
            $query->where('session_id', $request->session()->getId());
        }
        $cartItem = $query->where('product_id', $productId)->first();
        if (!$cartItem) {
            $cartItem = new Cart();
            if (Auth::check()) {
                $cartItem->user_id = Auth::id();
                $cartItem->session_id = $request->session()->getId();
            } else {
                $cartItem->session_id = $request->session()->getId();
                $cartItem->user_id = null;
            }
            $cartItem->product_id = $productId;
            $cartItem->quantity = 0;
        }

        $cartItem->quantity += $quantity;
        if ($cartItem->quantity > $product->quantity) {
            $cartItem->quantity = $product->quantity;
        }
        $cartItem->save();

        if ($request->ajax() || $request->wantsJson()) {
            $cartCount = Cart::where($key, $value)->sum('quantity');
            return response()->json(['success' => true, 'message' => 'Product added to cart!', 'cartCount' => $cartCount]);
        }
        return back()->with('success', 'Product added to cart!');
    }

    // update quantity (PUT)
    public function update(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        [$key, $value] = $this->getCartIdentifier($request);
        $cartItem = Cart::where($key, $value)->where('id', $itemId)->with('product')->firstOrFail();

        $max = (int)($cartItem->product->quantity ?? 99);
        if ($request->quantity > $max) {
            return response()->json(['success' => false, 'message' => 'Quantity exceeds stock', 'max' => $max], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // recalc totals to return
        $cartItems = Cart::where($key, $value)->with('product')->get();
        $subtotal = $cartItems->sum(fn($i) => $i->quantity * ($i->product->price ?? 0));
        $item_count = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'item_total' => $cartItem->quantity * ($cartItem->product->price ?? 0),
            'subtotal' => $subtotal,
            'item_count' => $item_count
        ]);
    }

    // remove (DELETE)
    public function remove(Request $request, $itemId)
    {
        [$key, $value] = $this->getCartIdentifier($request);

        Cart::where($key, $value)->where('id', $itemId)->delete();

        $cartItems = Cart::where($key, $value)->with('product')->get();
        $subtotal = $cartItems->sum(fn($i) => $i->quantity * ($i->product->price ?? 0));
        $item_count = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'subtotal' => $subtotal,
            'item_count' => $item_count,
            'items_count' => $cartItems->count()
        ]);
    }

    // alias to support resourceful destroy
    public function destroy(Request $request, $itemId)
    {
        return $this->remove($request, $itemId);
    }
}
