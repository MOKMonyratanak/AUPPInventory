@extends('layouts.app')

@section('content')
<div id="users-show-container" class="container">
    <h1 class="page-heading">
        <i class="fas fa-user"></i> User Details
    </h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-id-badge"></i> User ID: {{ $user->id }}
            </h5>
            <p class="card-text">
                <strong><i class="fas fa-user"></i> Name:</strong> {{ $user->name }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-envelope"></i> Email:</strong> {{ $user->email }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-user-tag"></i> Role:</strong> {{ $user->role }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-building"></i> Company Name:</strong> {{ $user->company->name ?? 'N/A' }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-briefcase"></i> Position:</strong> {{ $user->position->name }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-phone"></i> Contact Number:</strong> {{ $user->contact_number }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-info-circle"></i> Status:</strong> {{ ucfirst($user->status) }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-calendar-alt"></i> Created At:</strong> {{ $user->created_at->format('d-M-Y H:i') }}
            </p>
            <p class="card-text">
                <strong><i class="fas fa-calendar-alt"></i> Updated At:</strong> {{ $user->updated_at->format('d-M-Y H:i') }}
            </p>

            <div class="d-flex justify-content-start mt-3 mb-6">
                <!-- Edit Button -->
                @if(auth()->user()->role === 'admin' || 
                    (auth()->user()->role !== 'admin' && $user->role === 'user'))
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
            
                <!-- Delete Button -->
                @if(auth()->user()->role === 'admin')
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete-button">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                @endif
            
                <!-- Resign Button -->
                @if($user->status === 'employed' && 
                   (auth()->user()->role === 'admin' || 
                   (auth()->user()->role !== 'admin' && $user->role === 'user')))
                    <form action="{{ route('users.resign', $user->id) }}" method="POST" class="resign-form ms-2">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-secondary resign-button">
                            <i class="fas fa-user-slash"></i> Resign
                        </button>
                    </form>
                @endif

                <!-- Issue Asset Button -->
                @if ($user->status !== 'resigned')
                    <button type="button" class="btn btn-primary ms-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#purposeModal">
                        <i class="fas fa-box"></i> Issue Asset
                    </button>
                @endif

                <!-- History Button -->
                <a href="{{ route('users.history', $user->id) }}" class="btn btn-info ms-2">
                    <i class="fas fa-history"></i> View History
                </a>
            
                <!-- Back Button -->
                <a href="{{ route('users.index') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>            

            <!-- Purpose Modal -->
            <div class="modal fade" id="purposeModal" tabindex="-1" aria-labelledby="purposeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="purposeModalLabel">Select Purpose</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="purposeForm" action="{{ route('users.assets.issue', $user->id) }}" method="GET">
                                @csrf
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <select name="purpose" id="purpose" class="form-select" required>
                                        <option value="">Select Purpose</option>
                                        <option value="event">Event</option>
                                        <option value="daily_work">Daily Work</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="purposeForm" class="btn btn-primary">Proceed</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Issued Assets Section -->
            <div class="table-wrapper">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fas fa-box"></i> Issued Assets</h5>
                    </div>
                    <div class="card-body">
                        @if ($user->assets->isNotEmpty())
                        <div class="table-container">
                            <div class="table-scroll">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Asset Tag</th>
                                            <th>Device Type</th>
                                            <th class="col-status">Purpose</th>
                                            <th class="col-description">Date & Time</th>
                                            <th class="col-status">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->assets as $asset)
                                            <tr>
                                                <td><a href="{{ route('assets.show', $asset->asset_tag) }}">{{ $asset->asset_tag }}</a></td>
                                                <td>{{ $asset->deviceType->name ?? 'N/A' }}</td>
                                                <td>{{ $asset->purpose ?? 'N/A' }}</td>
                                                <td>{{ $asset->latestIssueLog ? $asset->latestIssueLog->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                                <td>
                                                    <form action="{{ route('users.return-asset', $user->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        <input type="hidden" name="asset_tag" value="{{ $asset->asset_tag }}">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-undo"></i> Return
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                            <p>No assets issued to this user.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.assetRoutes = {};
</script>
@endsection