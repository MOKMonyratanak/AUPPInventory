@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Top Section: Brand Pie Chart on the Left, KPIs on the Right -->
    <div class="row">
        <!-- Left: Brand Distribution Pie Chart (Spans 3 Rows) -->
        <div class="col-md-6">
            <div class="card mb-4" style="height: 100%;">
                <div class="card-header">Asset Distribution by Brand</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="brandChart" height="300"></canvas>
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
                    <canvas id="assetChart" height="600"></canvas>
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
                    <!-- Filter Dropdown -->
                    <form method="GET" action="{{ url()->current() }}" class="float-end">
                        <select name="filter" onchange="this.form.submit()" class="form-select form-select-sm w-auto d-inline">
                            <option value="last_10" {{ $activityFilter === 'last_10' ? 'selected' : '' }}>Last 10</option>
                            <option value="all" {{ $activityFilter === 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    @if($recentActivities->isEmpty())
                        <p class="text-muted">No recent activities available.</p>
                    @else
                        <ul class="list-group">
                            @foreach($recentActivities as $activity)
                                <li class="list-group-item">
                                    <strong>{{ $activity->user->name ?? 'Unknown User' }}</strong>
                                    {{ $activity->action }} asset <strong>{{ $activity->asset_tag }}</strong>
                                    @if($activity->affectedUser)
                                        for <strong>{{ $activity->affectedUser->name }}</strong>
                                    @endif
                                    (Purpose: {{ $activity->purpose }})
                                    <span class="text-muted float-end">{{ $activity->created_at }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart for Brand Distribution
    const ctxPie = document.getElementById('brandChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: @json($brandLabels),
            datasets: [{
                data: @json($brandCounts),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', 
                    '#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360', '#AC64AD',
                    '#2ECC71', '#1ABC9C', '#9B59B6', '#F1C40F', '#E67E22', '#E74C3C', 
                    '#3498DB', '#34495E', '#95A5A6', '#D35400', '#C0392B', '#7F8C8D', 
                    '#27AE60', '#16A085', '#2C3E50', '#BDC3C7', '#8E44AD', '#1F618D'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Asset Distribution by Brand' }
            }
        }
    });

    // Bar Chart for Device Types
    const ctxBar = document.getElementById('assetChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: @json($deviceTypeLabels),
            datasets: [
                {
                    label: 'Remaining Assets',
                    data: @json($remainingDeviceCounts),
                    backgroundColor: '#4CAF50',
                },
                {
                    label: 'Issued Assets',
                    data: @json($issuedDeviceCounts),
                    backgroundColor: '#FF6384',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Issued vs Remaining Assets by Device Type' }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Device Types' },
                    ticks: {
                        autoSkip: false, // Show all labels
                        maxRotation: 45, // Rotate labels for better readability
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Assets' }
                }
            }
        }
    });
});
</script>
@endsection
