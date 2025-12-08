<?php


namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class UserMangement extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized'); 
        }
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('id', 'asc')->paginate(5)->appends($request->query());

        return view('auth.user-management', ['users' => $users]);
    }

    public function edit($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);
        return view('user.edit-user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('user.user_management')->with('success', 'User updated!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.user_management')->with('success', 'User deleted!');
    }
}