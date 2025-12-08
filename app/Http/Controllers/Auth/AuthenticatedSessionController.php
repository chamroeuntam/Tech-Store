<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Merge session cart into user cart
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        $sessionCartItems = \App\Models\Cart::where('session_id', $sessionId)->get();
        foreach ($sessionCartItems as $item) {
            $existing = \App\Models\Cart::where('user_id', $userId)
                ->where('product_id', $item->product_id)
                ->first();
            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
                $item->delete();
            } else {
                $item->user_id = $userId;
                $item->session_id = null;
                $item->save();
            }
        }
        // Clean up: ensure all user cart items have session_id = null
        \App\Models\Cart::where('user_id', $userId)->update(['session_id' => null]);

        return redirect()->intended(route('profile.user_profile', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
