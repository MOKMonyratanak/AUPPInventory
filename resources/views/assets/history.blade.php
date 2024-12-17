@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-history"></i> History for Asset: {{ $asset->asset_tag }}
    </h1>

    <div class="card">
        <div class="card-body">
            <h5><strong>Asset Details</strong></h5>
            <p>Device Type: {{ $asset->deviceType->name ?? 'N/A' }}</p>
            <p>Brand: {{ $asset->brand->name ?? 'N/A' }}</p>
            <p>Status: {{ $asset->status }}</p>
            <p>Condition: {{ $asset->condition }}</p>
        </div>
    </div>

    <h3 class="mt-4">Activity Logs</h3>
    @if($activityLogs->isEmpty())
        <p>No activity logs found for this asset.</p>
    @else
    <div class="table-scroll">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-name"><i class="fas fa-calendar-alt"></i> Date</th>
                    <th class="col-name"><i class="fas fa-tasks"></i> Action</th>
                    <th class="col-name"><i class="fas fa-user-check"></i> Performed By</th>
                    <th class="col-name"><i class="fas fa-user"></i> User</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activityLogs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->performedBy->name ?? 'N/A' }}</td>
                    <td>{{ $log->affectedUser->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>        
    </div>

    
    @endif

    <a href="{{ route('assets.index') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Back to Assets
    </a>
</div>
@endsection
