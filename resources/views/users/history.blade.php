@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-history"></i> History for {{ $user->name }}
    </h1>

    <a href="{{ route('users.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>

    <h3 class="mt-4 mb-2 fw-bold">Activity Logs</h3>
    @if($history->isEmpty())
        <p>No history found for this user.</p>
    @else
    <div class="table-scroll">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-name"><i class="fas fa-calendar-alt"></i> Date & Time </th>
                    <th class="col-name"><i class="fas fa-user-check"></i> Admin </th>
                    <th class="col-name"><i class="fas fa-tasks"></i> Action </th>
                    <th class="col-name"><i class="fas fa-user"></i> Asset </th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->performedBy->name ?? 'N/A' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->asset_tag ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>        
    </div>
    <!-- Pagination Links -->
    <div class="mt-3">
        {{ $history->links() }}
    </div>
    @endif
</div>
@endsection
