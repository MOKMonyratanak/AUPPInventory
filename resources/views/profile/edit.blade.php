@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-user-cog"></i> Edit Profile
    </h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <!-- Only show the Role field if the user is not a manager -->
        @if ($user->role !== 'manager')
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
        @endif

        <!-- Only show the Company Name field if the user is not a manager -->
        @if ($user->role === 'admin')
            <div class="mb-3">
                <label for="company_id" class="form-label">Company</label>
                <select name="company_id" class="form-control" required>
                    <option value="" disabled>Select a company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>                
            </div>
        @endif

        {{-- <div class="mb-3">
            <label for="position" class="form-label">Position</label>
            <input type="text" class="form-control" id="position" name="position" value="{{ old('position', $user->position) }}" required>
        </div> --}}

        <div class="mb-3">
            <label for="position_id" class="form-label">Position</label>
            <select name="position_id" class="form-control" required>
                <option value="" disabled>Select a Position</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}" {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                    </option>
                @endforeach
            </select>                
        </div>

        <div class="mb-3">
            <label for="contact_number" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
@endsection
