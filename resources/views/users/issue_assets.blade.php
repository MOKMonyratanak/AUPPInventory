@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-box"></i> Issue Assets - Purpose: {{ ucfirst($purpose) }}
    </h1>

    <!-- Assets Section -->
    <form id="assetAssignmentForm" action="{{ route('users.assignAssets', $user->id) }}" method="POST">
        @csrf
        <input type="hidden" name="purpose" value="{{ $purpose }}">
        <div class="row">
            <!-- Available Assets -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-box"></i> Available Assets
                    </div>
                    <div class="card-body">
                        <select id="availableAssets" class="form-select" size="10" multiple>
                            @foreach($availableAssets as $asset)
                                <option value="{{ $asset->asset_tag }}">
                                    {{ $asset->asset_tag }} - {{ $asset->deviceType->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Arrows -->
            <div class="col-md-2 d-flex justify-content-center align-items-center">
                <div>
                    <button type="button" id="assignAsset" class="btn btn-primary mb-2">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button type="button" id="removeAsset" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
            </div>

            <!-- User's Assigned Assets -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-box"></i> Assets Assigned to {{ $user->name }} ({{ ucfirst($purpose) }})
                    </div>
                    <div class="card-body">
                        @if($purpose === 'daily_work')
                            <select id="userAssets" name="user_assets[daily_work][]" class="form-select" size="10" multiple>
                                @foreach($userAssets->where('purpose', 'daily_work') as $asset)
                                    <option value="{{ $asset->asset_tag }}">
                                        {{ $asset->asset_tag }} - {{ $asset->deviceType->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($purpose === 'event')
                            <select id="userAssets" name="user_assets[event][]" class="form-select" size="10" multiple>
                                @foreach($userAssets->where('purpose', 'event') as $asset)
                                    <option value="{{ $asset->asset_tag }}">
                                        {{ $asset->asset_tag }} - {{ $asset->deviceType->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Save and Print Buttons -->
        <div class="mt-3">
            <!-- Save Button -->
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save
            </button>

            <!-- Print Button -->
            <a href="{{ route('users.print', ['user' => $user->id, 'purpose' => $purpose]) }}" class="btn btn-warning ms-2">
                <i class="fas fa-print"></i> Print
            </a>    
            
            <!-- Email Button -->
            <a href="{{ route('users.emailPdf', ['user' => $user->id, 'purpose' => $purpose]) }}" class="btn btn-primary ms-2">
                <i class="fas fa-envelope"></i> Email PDF
            </a>
        </div>
    </form>
</div>

<!-- Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Move assets between lists
        function moveSelected(fromId, toId) {
            const from = document.getElementById(fromId);
            const to = document.getElementById(toId);
            const selectedOptions = Array.from(from.selectedOptions);

            selectedOptions.forEach(option => {
                from.removeChild(option);
                to.appendChild(option);
            });
        }

        // Assign button click
        document.getElementById('assignAsset').addEventListener('click', function () {
            moveSelected('availableAssets', 'userAssets');
        });

        // Remove button click
        document.getElementById('removeAsset').addEventListener('click', function () {
            moveSelected('userAssets', 'availableAssets');
        });

        // Ensure all selected assets are submitted
        document.getElementById('assetAssignmentForm').addEventListener('submit', function () {
            document.querySelectorAll('select[multiple]').forEach(select => {
                Array.from(select.options).forEach(option => option.selected = true);
            });
        });
    });
</script>
@endsection