@extends('layouts.app')

@section('content')
<div id="dashboard-container" class="container">
    <!-- Filter Section -->
    @if(auth()->user()->role === 'admin')
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="company_id" class="form-label">Filter by Company</label>
                <select name="company_id" id="company_id" class="form-control">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    @endif

    <!-- Top Section: Brand Pie Chart on the Left, KPIs on the Right -->
    <div class="row">
        <!-- Left: Brand Distribution Pie Chart (Spans 3 Rows) -->
        <div class="col-md-6">
            <div class="card mb-4" style="height: 100%;">
                <div class="card-header">Asset Distribution by Brand</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas 
                        id="brandChart" 
                        height="300" 
                        data-labels='@json($brandLabels)' 
                        data-counts='@json($brandCounts)'>
                    </canvas>
                </div>
            </div>
        </div>
        <!-- Right: KPIs Stacked Top to Bottom -->
        <div class="col-md-6 d-flex flex-column">
            <div class="card mb-3 flex-grow-1 text-center">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Total Assets</h5>
                    <p class="card-text display-6">{{ $totalAssets }}</p>
                </div>
            </div>
            <div class="card mb-3 flex-grow-1 text-center">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Issued Assets</h5>
                    <p class="card-text display-6">{{ $issuedAssets }}</p>
                </div>
            </div>
            <div class="card flex-grow-1 text-center">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title">Remaining Assets</h5>
                    <p class="card-text display-6">{{ $remainingAssets }}</p>
                </div>
            </div>
        </div>

        <!-- Middle Section: Issued Assets by Device Type -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Issued Assets by Device Type</div>
                    <div class="card-body" style="width: 100%; overflow-x: auto;">
                        <canvas 
                            id="assetChart" 
                            height="600" 
                            data-device-type-labels='@json($deviceTypeLabels)' 
                            data-remaining-device-counts='@json($remainingDeviceCounts)' 
                            data-issued-device-counts='@json($issuedDeviceCounts)'>
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Recent Activity -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Recent Activity
                    </div>
                    <div class="card-body">
                        @if($recentActivities->isEmpty())
                            <p class="text-muted">No recent activities available.</p>
                        @else
                        <div class="table-container">
                            <div class="table-scroll">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="col-name"><i class="fas fa-calendar-alt"></i> Date & Time </th>
                                            <th class="col-name"><i class="fas fa-user-check"></i> Admin </th>
                                            <th class="col-name"><i class="fas fa-tasks"></i> Action </th>
                                            <th class="col-name"><i class="fas fa-user"></i> User </th>
                                            <th class="col-name"><i class="fas fa-box"></i> Asset </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $activity->performedBy->name ?? 'N/A' }}</td>
                                            <td>{{ $activity->action }}</td>
                                            <td>{{ $activity->affectedUser->name ?? 'N/A' }}</td>
                                            <td>{{ $activity->asset_tag ?? 'N/A'}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>        
                            </div>
                        </div>
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{ $recentActivities->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.assetRoutes = {};
</script>
@endsection