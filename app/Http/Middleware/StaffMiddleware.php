<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // staff can be: staff OR admin
        if (!in_array(Auth::user()->role, ['staff', 'admin'])) {
            abort(403, 'Unauthorized: Staff only.');
        }

        return $next($request);
    }
}
