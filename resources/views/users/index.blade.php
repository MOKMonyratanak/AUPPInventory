@extends('layouts.app')

@section('content')
<div id="users-index-container" class="container">
    <h1 class="page-heading">
        <i class="fas fa-users"></i> Users
    </h1>
    
    <!-- Search Form -->
    <form action="{{ route('users.index') }}" method="GET" class="mb-3 d-flex">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ htmlspecialchars(request()->input('search')) }}">
            <button class="btn btn-outline-secondary button-search" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        @if(request()->has('search'))
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary ms-4 clear-btn">
                <i class="fas fa-times-circle"></i> Clear
            </a>
        @endif
    </form>

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-user-plus"></i> Add User
    </a>

    <div class="table-container">
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
                               (auth()->user()->role !== 'admin' && $user->role === 'user'))
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if(auth()->user() && auth()->user()->role === 'admin')
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm delete-button">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            @endif
                        
                            @if($user->status === 'employed' && (auth()->user()->role === 'admin' || 
                               (auth()->user()->role !== 'admin' && $user->role === 'user')))
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
    
                            <!-- History Button -->
                            <a href="{{ route('users.history', $user->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-history"></i> View History
                            </a>
                        </td>                
                    </tr>
                    @endforeach
                </tbody>
            </table>       
        </div>
    </div>
    {{-- Pagination --}}
    <div class="mt-3">
        {{ $users->appends(request()->except('page'))->links() }}
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
</div>
<script>
    window.assetRoutes = {};
</script>
@endsection






