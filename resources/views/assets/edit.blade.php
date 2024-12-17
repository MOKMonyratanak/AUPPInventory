@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-edit"></i> Edit Asset
    </h1>

    <!-- Error Alert Section -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li> <!-- Display validation errors including duplicate asset_tag -->
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('assets.update', $asset->asset_tag) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Asset Tag -->
        <div class="form-group mb-3">
            <label for="asset_tag">
                <i class="fas fa-tag"></i> Asset Tag
            </label>
            <input type="text" name="asset_tag" class="form-control" value="{{ old('asset_tag', $asset->asset_tag) }}" required>
            @error('asset_tag')
                <div class="text-danger">{{ $message }}</div> <!-- Show validation error for asset_tag -->
            @enderror
        </div>

        <!-- Device Type (Dropdown) -->
        <div class="form-group mb-3">
            <label for="device_type_id">
                <i class="fas fa-desktop"></i> Device Type
            </label>
            <select name="device_type_id" id="device_type_id" class="form-control select2" required>
                <option value="" disabled>Select a Device Type</option>
                @foreach($deviceTypes as $deviceType)
                    <option value="{{ $deviceType->id }}" {{ old('device_type_id', $asset->device_type_id) == $deviceType->id ? 'selected' : '' }}>
                        {{ $deviceType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Brand -->
        <div class="form-group mb-3">
            <label for="brand">
                <i class="fas fa-industry"></i> Brand
            </label>
            <select name="brand_id" id="brand_id" class="form-control select2" required>
                <option value="" disabled>Select a Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $asset->brand_id) == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Model -->
        <div class="form-group mb-3">
            <label for="model">
                <i class="fas fa-cube"></i> Model
            </label>
            <input type="text" name="model" class="form-control" value="{{ old('model', $asset->model) }}">
        </div>

        <!-- Serial Number -->
        <div class="form-group mb-3">
            <label for="serial_no">
                <i class="fas fa-barcode"></i> Serial Number
            </label>
            <input type="text" name="serial_no" class="form-control" value="{{ old('serial_no', $asset->serial_no) }}">
        </div>

        <!-- Condition -->
        <div class="form-group mb-3">
            <label for="condition">
                <i class="fas fa-clipboard-check"></i> Condition
            </label>
            <select name="condition" class="form-control" required>
                <option value="new" {{ $asset->status == 'new' ? 'selected' : '' }}>New</option>
                <option value="moderate" {{ $asset->status == 'moderate' ? 'selected' : '' }}>Moderate</option>
                <option value="poor" {{ $asset->status == 'poor' ? 'selected' : '' }}>Poor</option>
                <option value="defective" {{ $asset->status == 'defective' ? 'selected' : '' }}>Defective</option>
                <option value="for repair" {{ $asset->status == 'for repair' ? 'selected' : '' }}>For Repair</option>
            </select>
        </div>

        <!-- Status -->
        <div class="form-group mb-3">
            <label for="status">
                <i class="fas fa-info-circle"></i> Status
            </label>
            <select name="status" class="form-control" required>
                <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="issued" {{ $asset->status == 'issued' ? 'selected' : '' }}>Issued</option>
            </select>
        </div>

        <!-- Company (Dropdown) -->
        <div class="form-group mb-3">
            <label for="company_id">
                <i class="fas fa-building"></i> Company
            </label>
            @if(auth()->user()->role === 'manager')
                <!-- Managers can only see and edit their own company, so show a fixed input -->
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                <input type="text" class="form-control" value="{{ auth()->user()->company->name }}" disabled>
            @else
                <!-- Show dropdown for other roles -->
                <select name="company_id" class="form-control" required>
                    <option value="" disabled>Select a Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $asset->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- Note -->
        <div class="form-group mb-3">
            <label for="asset_tag">
                <i class="fas fa-info-circle"></i> Note
            </label>
            <textarea name="note" class="form-control">{{ old('note', $asset->note) }}</textarea>
        </div>


        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update Asset
        </button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select a Device Type",
            allowClear: true
        });
    });
</script>

@endsection
