@extends('layouts.app')
@section('title', 'User Profile - IT Store')
@section('content')
@vite(['resources/css/edit-user.css'])

<div class="profile-wrapper">
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-edit icon"></i> Edit User</h1>
            <p><i class="fas fa-user"></i> <strong>Username:</strong> {{ $user->username }}</p>
            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> {{ $user->email }}</p>
            <p class="mb-3">Update user role below</p>
        </div>
        <form id="edit-user-form" method="POST" action="{{ route('user.update_user', $user->id) }}">
            @csrf
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update User
            </button>
        </form>
    </div>
</div>
@endsection