@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-edit"></i> Edit Device Type
    </h1>

    <form action="{{ route('device_types.update', $deviceType->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group mb-3">
            <label for="name">
                <i class="fas fa-tag"></i> Name
            </label>
            <input type="text" name="name" class="form-control" value="{{ $deviceType->name }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="description">
                <i class="fas fa-align-left"></i> Description
            </label>
            <textarea name="description" class="form-control">{{ $deviceType->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update
        </button>
    </form>
</div>
@endsection
