@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-users"></i> Users
    </h1>
    
    <!-- Search Form -->
    <form action="{{ route('users.index') }}" method="GET" class="mb-3 d-flex">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ htmlspecialchars(request()->input('search')) }}">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        @if(request()->has('search'))
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary ms-2 clear-btn">
                <i class="fas fa-times-circle"></i> Clear
            </a>
        @endif
    </form>

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-user-plus"></i> Add User
    </a>

    <div class="table-scroll">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-id"><i class="fas fa-id-badge"></i> ID</th>
                    <th class="col-name"><i class="fas fa-user"></i> Name</th>
                    <th class="col-name"><i class="fas fa-envelope"></i> Email</th>
                    <th class="col-status"><i class="fas fa-user-tag"></i> Role</th>
                    <th class="col-name"><i class="fas fa-building"></i> Company Name</th>
                    <th class="col-actions"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><a href="{{ route('users.show', $user->id) }}">{{ $user->id }}</a></td>
                    <td><a href="{{ route('users.show', $user->id) }}" class="text-primary">{{ $user->name }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ optional($user->company)->name ?? 'N/A' }}</td>
                    <td>
                        @if(auth()->user()->role === 'admin' || 
                           (auth()->user()->role === 'manager' && $user->role === 'user'))
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        @endif
                    
                        @if($user->status === 'employed' && (auth()->user()->role === 'admin' || 
                           (auth()->user()->role === 'manager' && $user->role === 'user')))
                            <form action="{{ route('users.resign', $user->id) }}" method="POST" class="resign-form" style="display:inline-block;">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="btn btn-secondary btn-sm resign-button">
                                    <i class="fas fa-user-slash"></i> Resign
                                </button>
                            </form>
                        @endif

                        <!-- Issue Asset Button -->
                        @if ($user->status !== 'resigned')
                            <button type="button" class="btn btn-primary btn-sm issue-asset-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#purposeModal" 
                                data-user-id="{{ $user->id }}">
                                <i class="fas fa-box"></i> Issue Asset
                            </button>
                        @endif

                    </td>                
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for Error Messages -->
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
                <form id="purposeForm" action="#" method="GET">
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

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the error modal
    let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    // Get the error message from the session, if any
    let isErrorModalShown = @json(session('issuedAssets', false));
    let errorMessage = @json(session('error'));

    if (isErrorModalShown && errorMessage) {
        // Display the error message in the modal body
        document.getElementById('errorModalBody').innerText = errorMessage;
        
        // Show the modal
        errorModal.show();
    }

    // Add event listener for resign buttons
    document.querySelectorAll('.resign-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            // Perform AJAX request to check for issued assets
            const userId = this.closest('.resign-form').getAttribute('action').split('/').slice(-2, -1)[0];
            const checkIssuedAssetsUrl = `/users/${userId}/check-issued-assets`;

            fetch(checkIssuedAssetsUrl, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasIssuedAssets) {
                    // Show error modal if assets are issued
                    document.getElementById('errorModalBody').innerText = "The user cannot resign until all issued assets are returned to the company.";
                    errorModal.show();
                } else {
                    // Proceed with confirmation dialogs if no assets are issued
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

document.addEventListener('DOMContentLoaded', function() {
    const purposeForm = document.getElementById('purposeForm');

    document.querySelectorAll('.issue-asset-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            if (userId) {
                purposeForm.setAttribute('action', `/users/${userId}/assets/issue`);
            } else {
                purposeForm.setAttribute('action', '#');
            }
        });
    });
});


//Sanitize input
document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.querySelector('form[action="{{ route('users.index') }}"]');
    const searchInput = searchForm.querySelector('input[name="search"]');

    searchForm.addEventListener('submit', function (event) {
        const userInput = searchInput.value;

        // Create a temporary element to leverage the browser's HTML entity encoding
        const tempElement = document.createElement('div');
        tempElement.textContent = userInput;  // This will automatically encode special characters to HTML entities
        const encodedInput = tempElement.innerHTML;  // Get the HTML-encoded content

        // Update the input field with the encoded value
        searchInput.value = encodedInput;
    });
});



</script>
@endsection
