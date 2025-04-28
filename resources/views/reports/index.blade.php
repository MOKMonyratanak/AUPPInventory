@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-chart-bar"></i> Reports
    </h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('reports.index') }}" class="mb-4">
        <div class="row">
            @if(auth()->user()->role === 'admin')
            <div class="col-md-3">
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
            @endif
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Activity Report -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Activity Report</h5>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Device Type</th>
                        <th>Issued</th>
                        <th>Returned</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($reportData))
                        @foreach ($reportData as $deviceType => $actions)
                            <tr>
                                <td>{{ $deviceType }}</td>
                                <td>{{ $actions['issue'] ?? 0 }}</td>
                                <td>{{ $actions['return'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center">Please enter a date range and click "Filter" to view the report.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Device Type Report -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Device Type Report</h5>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Device Type</th>
                        <th>Available</th>
                        <th>Issued</th>
                        <th>Non-Issuable</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($deviceTypeReport))
                        @foreach ($deviceTypeReport as $deviceType => $counts)
                            <tr>
                                <td>{{ $deviceType }}</td>
                                <td>{{ $counts['available'] }}</td>
                                <td>{{ $counts['issued'] }}</td>
                                <td>{{ $counts['non_issuable'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection