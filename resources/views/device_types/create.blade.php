@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-desktop"></i> Create New Device Type
    </h1>

    <form action="{{ route('device_types.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">
                <i class="fas fa-tag"></i> Name
            </label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="description">
                <i class="fas fa-align-left"></i> Description
            </label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create
        </button>
    </form>
</div>
@endsection
