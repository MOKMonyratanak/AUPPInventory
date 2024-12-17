@extends('layouts.app')

@section('content')
<div class="container">
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
                <strong><i class="fas fa-briefcase"></i> Position:</strong> {{ $user->position }}
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
                    (auth()->user()->role === 'manager' && $user->role === 'user'))
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
            
                <!-- Delete Button -->
                @if(auth()->user()->role === 'admin')
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                @endif
            
                <!-- Resign Button -->
                @if($user->status === 'employed' && 
                   (auth()->user()->role === 'admin' || 
                   (auth()->user()->role === 'manager' && $user->role === 'user')))
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
            
                <!-- Back Button -->
                <a href="{{ route('users.index') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>            

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Action Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                    <!-- Error message will be set here via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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

</div>


<script>

    // Script for resign button
    document.addEventListener('DOMContentLoaded', function() {
    let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    document.querySelectorAll('.resign-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            
            const userId = this.closest('.resign-form').getAttribute('action').split('/').slice(-2, -1)[0];
            const checkIssuedAssetsUrl = `/users/${userId}/check-issued-assets`;

            fetch(checkIssuedAssetsUrl, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasIssuedAssets) {
                    document.getElementById('errorModalBody').innerText = "The user cannot resign until all issued assets are returned to the company.";
                    errorModal.show();
                } else {
                    if (confirm("Have all the assets been returned?")) {
                        if (confirm("Are you sure all assets have been returned?")) {
                            this.closest('.resign-form').submit();
                        }
                    }
                }
            })
            .catch(error => console.error("Error checking issued assets:", error));
        });
    });
});
</script>
@endsection
