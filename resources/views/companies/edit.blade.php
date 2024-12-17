@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-edit"></i> Edit Company
    </h1>

    <form action="{{ route('companies.update', $company->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-tag"></i> Company Name
            </label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">
                <i class="fas fa-align-left"></i> Description
            </label>
            <textarea name="description" class="form-control">{{ old('description', $company->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Company
        </button>
    </form>
</div>
@endsection
