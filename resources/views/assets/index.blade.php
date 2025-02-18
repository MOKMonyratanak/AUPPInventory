@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-box"></i> Assets
    </h1>

    <!-- Search Form -->
    <form action="{{ route('assets.index') }}" method="GET" class="mb-3 d-flex">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search assets..." value="{{ htmlspecialchars(request()->input('search')) }}">
            <button class="btn btn-outline-secondary button-search" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        @if(request()->has('search'))
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary ms-4 clear-btn" >
                <i class="fas fa-times-circle"></i> Clear
            </a>
        @endif
    </form>

    <a href="{{ route('assets.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus-circle"></i> Add Asset
    </a>

    <div class="table-container">
        <div class="table-scroll">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-name"><i class="fas fa-tag"></i> Asset Tag</th>
                        <th class="col-name"><i class="fas fa-desktop"></i> Device Type</th>
                        <th class="col-status"><i class="fas fa-info-circle"></i> Status</th>
                        <th class="col-purpose"><i class="fas fa-info-circle"></i> Purpose</th>
                        <th class="col-name"><i class="fas fa-user"></i> Assigned User</th>
                        <th class="col-name"><i class="fas fa-user-check"></i> Checked Out By</th>
                        <th class="col-name"><i class="fas fa-building"></i> Company Name</th>
                        <th class="col-actions"><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                    <tr>
                        <td class="col-name"><a href="{{ route('assets.show', $asset->asset_tag) }}" class="text-primary">{{ $asset->asset_tag }}</a></td>
                        <td class="col-name">{{ $asset->deviceType ? $asset->deviceType->name : 'N/A' }}</td> <!-- Using the deviceType relationship -->
                        <td class="col-status">{{ $asset->status }}</td>
                        <td class="col-purpose">{{ $asset->purpose }}</td>
                        <td class="col-name">{{ $asset->user ? $asset->user->name : 'N/A' }}</td> <!-- Using the user relationship -->
                        <td class="col-name">{{ $asset->checkedOutBy ? $asset->checkedOutBy->name : 'N/A' }}</td> <!-- Using the checkedOutBy relationship -->
                        <td class="col-name">{{ $asset->company ? $asset->company->name : 'N/A' }}</td>
                        <td class="col-actions">
                            <a href="{{ route('assets.edit', $asset->asset_tag) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('assets.clone', $asset->asset_tag) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-clone"></i> Clone
                            </a>
                            <a href="{{ route('assets.history', $asset->asset_tag) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-history"></i> History
                            </a>                  
                            @if(auth()->user() && auth()->user()->role === 'admin')      
                                <form action="{{ route('assets.destroy', $asset->asset_tag) }}" method="POST" style="display:inline-block;" >
                                    @csrf
                                    @method('DELETE')
                                    <button data-delete-btn type="submit" class="btn btn-danger btn-sm" data-status="{{ $asset->status }}">
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
    {{-- Pagination --}}
    <div class="mt-3">
        {{ $assets->appends(request()->except('page'))->links() }}
    </div>
</div>
<script>
    window.assetRoutes = {
        index: @json(route('assets.index'))
    };
</script>
@endsection
