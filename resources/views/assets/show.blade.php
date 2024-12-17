@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-box"></i> Asset Details
    </h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-tag"></i> Asset Tag: {{ $asset->asset_tag }}
            </h5>
            <p class="card-text">
                <strong><i class="fas fa-desktop"></i> Device Type:</strong> {{ $asset->deviceType ? $asset->deviceType->name : 'N/A' }}
            </p> <!-- Using the deviceType relationship -->
            <p class="card-text">
                <strong><i class="fas fa-industry"></i> Brand:</strong> {{ $asset->brand ? $asset->brand->name : 'N/A' }}
            </p>            
            <p class="card-text">
                <strong><i class="fas fa-cube"></i> Model:</strong> {{ $asset->model ?? 'N/A' }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-barcode"></i> Serial Number:</strong> {{ $asset->serial_no ?? 'N/A' }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-clipboard-check"></i> Condition:</strong> {{ $asset->condition }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-info-circle"></i> Status:</strong> {{ $asset->status }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-user"></i> Assigned User:</strong> {{ $asset->user ? $asset->user->name : 'N/A' }}
            </p> <!-- Using the user relationship -->
            <p class="card-text">
                <strong><i class="fas fa-user-check"></i> Checked Out By:</strong> {{ $asset->checkedOutBy ? $asset->checkedOutBy->name : 'N/A' }}
            </p> <!-- Using the checkedOutBy relationship -->
            <p class="card-text">
                <strong><i class="fas fa-building"></i> Company:</strong> {{ $asset->company ? $asset->company->name : 'N/A' }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-bullseye"></i> Purpose:</strong> {{ $asset->purpose ? $asset->purpose : 'N/A' }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-calendar-alt"></i> Created At:</strong> {{ $asset->created_at->format('d-M-Y H:i') }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-calendar-alt"></i> Updated At:</strong> {{ $asset->updated_at->format('d-M-Y H:i') }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-info-circle"></i> Note:</strong> {{ $asset->note }}
            </p>

            <div class="d-flex justify-content-start mt-3">
                <!-- Edit Button -->
                <a href="{{ route('assets.edit', $asset->asset_tag) }}" class="btn btn-warning me-3">
                    <i class="fas fa-edit"></i> Edit
                </a>
            
                <!-- Clone Button -->
                <a href="{{ route('assets.clone', $asset->asset_tag) }}" class="btn btn-info me-2">
                    <i class="fas fa-clone"></i> Clone
                </a>

                <!-- History Button -->
                <a href="{{ route('assets.history', $asset->asset_tag) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-history"></i> History
                </a>

            
                <!-- Delete Button -->
                <form action="{{ route('assets.destroy', $asset->asset_tag) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
            
                <!-- Back Button -->
                <a href="{{ route('assets.index') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Assets
                </a>
            </div>
            
        </div>
    </div>
</div>
@endsection
