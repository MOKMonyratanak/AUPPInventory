@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">Profile</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <!-- Name -->
            <div class="mb-3">
                <strong>Name:</strong>
                <p>{{ $user->name }}</p>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <strong>Email:</strong>
                <p>{{ $user->email }}</p>
            </div>

            <!-- Role -->
            <div class="mb-3">
                <strong>Role:</strong>
                <p>{{ ucfirst($user->role) }}</p>
            </div>

            <!-- Company Name -->
            <div class="mb-3">
                <strong>Company Name:</strong>
                <p>{{ $user->company->name ?? 'N/A' }}</p>
            </div>

            <!-- Position -->
            <div class="mb-3">
                <strong>Position:</strong>
                <p>{{ $user->position }}</p>
            </div>

            <!-- Contact Number -->
            <div class="mb-3">
                <strong>Contact Number:</strong>
                <p>{{ $user->contact_number }}</p>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
    </div>
</div>
@endsection
