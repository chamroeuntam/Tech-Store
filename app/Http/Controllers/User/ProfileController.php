<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->get();
        $wishlists = Wishlist::where('user_id', $user->id)->get();
        $cartItems = Cart::where('user_id', $user->id)->get();
        $total_orders = $order->count();
        $total_wishlist_items = $wishlists ? $wishlists->count() : 0;
        $total_cart_items = $cartItems ? $cartItems->count() : 0;

        return view('profile.user-profile', [
            'user' => $user,
            'profile' => $user,
            'total_orders' => $total_orders,
            'total_wishlist_items' => $total_wishlist_items,
            'total_cart_items' => $total_cart_items,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit-profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate input
        $data = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number'   => 'nullable|string|max:20',
            'date_of_birth'  => 'nullable|date',
            'address'        => 'nullable|string|max:255',
            'role'           => 'nullable|string|max:50',
            'profile_picture'=> 'nullable|image|max:2048',
            'telegram_id'    => 'nullable|string|max:255',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {

            // Delete old image if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Upload new
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        } else {
            unset($data['profile_picture']);
        }

        User::where('id', $user->id)->update($data);

        return Redirect::route('profile.user_profile')
            ->with('status', 'Profile updated successfully!');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile picture
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
