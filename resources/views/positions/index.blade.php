@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-briefcase"></i> Positions
    </h1>

    <a href="{{ route('positions.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus-circle"></i> Add New Position
    </a>

    <div class="table-container">
        <div class="table-scroll">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="col-id"><i class="fas fa-hashtag"></i> ID</th>
                        <th class="col-name"><i class="fas fa-tag"></i> Name</th>
                        <th class="col-description"><i class="fas fa-align-left"></i> Description</th>
                        <th class="col-name"><i class="fas fa-calendar-plus"></i> Created At</th>
                        <th class="col-name"><i class="fas fa-sync-alt"></i> Updated At</th>
                        <th class="col-actions"><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($positions as $position)
                    <tr>
                        <td>{{ $position->id }}</td>
                        <td>{{ $position->name }}</td>
                        <td>{{ $position->description}}</td>
                        <td>{{ $position->created_at}}</td>
                        <td>{{ $position->updated_at}}</td>
                        <td>
                            <a href="{{ route('positions.edit', $position->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            {{-- Show Delete Button to Admin Users Only --}}
                            @if(auth()->user() && auth()->user()->role === 'admin')
                                <form action="{{ route('positions.destroy', $position->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button data-delete-btn type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    window.assetRoutes = {};
</script>
@endsection
