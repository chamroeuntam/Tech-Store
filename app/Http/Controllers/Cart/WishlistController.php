<?php


namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Schema;


class WishlistController extends Controller
{

    public function index(Request $request) {
        $sessionId = $request->session()->getId();
        $user = Auth::user();

        $wishlistQuery = Wishlist::with('product');

        if ($user && Schema::hasColumn('wishlists', 'user_id')) {
            $wishlistQuery->where('user_id', $user->id);
        } else {
            $wishlistQuery->where('session_id', $sessionId);
        }

        $wishlistItems = $wishlistQuery->get();
        
        $items = $wishlistItems->map(function ($item) {
            if (!$item->product) {
                return null;
            }

            return [
                'id' => $item->id,
                'product_id' => $item->product->id,
                'name' => $item->product->name,
                'price' => $item->product->price,
                'count' => $item->quantity ?? 1,
                'stock' => $item->product->quantity ?? 0, 
                'in_stock' => ($item->product->quantity ?? 0) > 0,
                'quantity' => $item->quantity ?? 1,
                'image_url' => $item->product->image_url ?? 'https://via.placeholder.com/80', // Fallback image
            ];
        })->filter()->values();
    $wishlist_count = $items->count();

        return view('wishlist.index', compact('items', 'wishlist_count'));
    }


    public function add(Request $request, $productId) {

        $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = (int) $request->input('quantity', 1);

        $attributes = ['product_id' => $productId];
        if (Auth::check() && Schema::hasColumn('wishlists', 'user_id')) {
            $attributes['user_id'] = Auth::id();
        } else {
            $attributes['session_id'] = $request->session()->getId();
        }

        if (Schema::hasColumn('wishlists', 'quantity')) {
            Wishlist::updateOrCreate($attributes, ['quantity' => $quantity]);
        } else {
            Wishlist::firstOrCreate($attributes);
        }

        if ($request->ajax() || $request->wantsJson()) {
            // Optionally, return wishlist count if needed
            $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
            return response()->json(['success' => true, 'message' => 'Added to wishlist!', 'wishlistCount' => $wishlistCount]);
        }
        return back()->with('success', 'Added to wishlist!');
    }

    public function destroy(Request $request, $itemId) {
        if (Auth::check() && Schema::hasColumn('wishlists', 'user_id')) {
            Wishlist::where('user_id', Auth::id())->where('id', $itemId)->delete();
        } else {
            Wishlist::where('session_id', $request->session()->getId())->where('id', $itemId)->delete();
        }
        return back()->with('success', 'Removed from wishlist!');
    }
}