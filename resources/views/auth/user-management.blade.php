@extends('layouts.app')

@section('content')
    @vite(['resources/css/user-management.css'])
    <div class="user-mgmt-wrapper">
        <div class="user-mgmt-container">
            <div class="user-mgmt-header">
                <h1><i class="fas fa-users-cog icon"></i> User Management</h1>
                <form method="GET" action="{{ route('user.user_management') }}" class="row g-2 align-items-center justify-content-center mb-3">
                    <div class="col-auto">
                        <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                    </div>
                    <div class="col-auto">
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                    </div>
                </form>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->first_name }}</td>
                        <td>{{ $u->last_name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ ucfirst($u->role) }}</td>
                        <td>
                            <a href="{{ route('user.edit_user', $u->id) }}" class="btn btn-primary">Edit</a>
                            <a href="{{ route('user.destroy', $u->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination justify-content-center">
                <div class="d-flex flex-column align-items-center ">
                    <div class=" text-center w-100">
                        {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>            
        </div>
    </div>
@endsection