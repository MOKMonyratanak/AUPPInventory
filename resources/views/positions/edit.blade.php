@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-briefcase"></i> Edit Position
    </h1>

    <form action="{{ route('positions.update', $position->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="form-group mb-3">
            <label for="name">
                <i class="fas fa-tag"></i> Position Name
            </label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $position->name) }}" required>
        </div>

        <!-- Description -->
        <div class="form-group mb-3">
            <label for="description">
                <i class="fas fa-info-circle"></i> Description
            </label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $position->description) }}</textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Position
        </button>
    </form>
</div>
@endsection
