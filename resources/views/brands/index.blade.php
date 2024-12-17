@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-tags"></i> Brands
    </h1>

    <a href="{{ route('brands.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus-circle"></i> Add New Brand
    </a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-scroll">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="col-id"><i class="fas fa-hashtag"></i> ID</th>
                    <th class="col-name"><i class="fas fa-tag"></i> Name</th>
                    <th class="col-description"><i class="fas fa-align-left"></i> Description</th>
                    <th class="col-actions"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ $brand->description}}</td>
                    <td>
                        <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
