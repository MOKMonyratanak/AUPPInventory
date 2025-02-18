@extends('layouts.app')

@section('content')
<div id="user-create-container" class="container">
    <h1 class="page-heading">
        <i class="fas fa-user-plus"></i> Create New User
    </h1>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <!-- User ID -->
        <div class="form-group mb-3">
            <label for="id">
                <i class="fas fa-id-card"></i> User ID
            </label>
            <input type="text" name="id" class="form-control" value="{{ old('id') }}" required>
            @error('id')
                <div class="text-danger">{{ $message }}</div> <!-- Show ID validation error -->
            @enderror
        </div>

        <!-- Name -->
        <div class="form-group mb-3">
            <label for="name">
                <i class="fas fa-user"></i> Name
            </label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <!-- Email -->
        <div class="form-group mb-3">
            <label for="email">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <!-- Role -->
        <div class="form-group mb-3">
            <label for="role">
                <i class="fas fa-user-tag"></i> Role
            </label>
            @if(auth()->user()->role === 'admin')
                <!-- Only show the dropdown for Admin users -->
                <select name="role" id="role" class="form-control" required onchange="togglePasswordFields()">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            @else
                <!-- Automatically assign "user" role for non-admins (hidden input) -->
                <input type="hidden" name="role" value="user">
                <input type="text" class="form-control" value="User" disabled>
            @endif
        </div>

        <!-- Password Fields (Only show for non-user roles) -->
        <div id="passwordFields" style="display: none;">
            <div class="form-group mb-3">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group mb-3">
                <label for="password_confirmation">
                    <i class="fas fa-lock"></i> Verify Password
                </label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>

        <!-- Company (Dropdown) -->
        <div class="form-group mb-3">
            <label for="company_id">
                <i class="fas fa-building"></i> Company
            </label>
            @if(auth()->user()->role === 'manager')
                <!-- Only allow the manager's company, disable the dropdown -->
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                <input type="text" class="form-control" value="{{ auth()->user()->company->name }}" disabled>
            @else
                <!-- Show dropdown for other roles -->
                <select name="company_id" class="form-control" required>
                    <option value="" disabled selected>Select a Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- Position (New) -->
        <div class="form-group mb-3">
            <label for="position_id">
                <i class="fas fa-briefcase"></i> Position
            </label>
            <select name="position_id" id="position_id" class="form-control select2" required>
                <option value="" disabled selected>Select a Position</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}" {{ old('position_id', $user['position_id'] ?? '') == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Contact Number -->
        <div class="form-group mb-3">
            <label for="contact_number">
                <i class="fas fa-phone"></i> Contact Number
            </label>
            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" required pattern="0\d{8,9}">
        </div>

        <!-- Status -->
        <div class="form-group mb-3">
            <label for="status">
                <i class="fas fa-info-circle"></i> Status
            </label>
            <select name="status" class="form-control" required>
                <option value="employed" {{ old('status') == 'employed' ? 'selected' : '' }}>Employed</option>
                <option value="resigned" {{ old('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create User
        </button>
    </form>
</div>
<script>
    window.assetRoutes = {};
</script>
@endsection
