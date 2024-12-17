@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-building"></i> Add Company
    </h1>

    <form action="{{ route('companies.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-tag"></i> Company Name
            </label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">
                <i class="fas fa-align-left"></i> Description
            </label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Add Company
        </button>
    </form>
</div>
@endsection
