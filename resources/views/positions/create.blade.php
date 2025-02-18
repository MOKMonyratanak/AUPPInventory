@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-briefcase"></i> Create New Position
    </h1>

    <form action="{{ route('positions.store') }}" method="POST">
        @csrf

        <!-- Name -->
        <div class="form-group mb-3">
            <label for="name">
                <i class="fas fa-tag"></i> Position Name
            </label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <!-- Description -->
        <div class="form-group mb-3">
            <label for="description">
                <i class="fas fa-info-circle"></i> Description
            </label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create Position
        </button>
    </form>
</div>
@endsection
