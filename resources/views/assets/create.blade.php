@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-heading">
        <i class="fas fa-box"></i> Add New Asset
    </h1>

    <form action="{{ route('assets.store') }}" method="POST">
        @csrf

        <!-- Asset Tag -->
        <div class="form-group mb-3">
            <label for="asset_tag">
                <i class="fas fa-tag"></i> Asset Tag
            </label>
            <input type="text" name="asset_tag" class="form-control" value="{{ old('asset_tag', $asset['asset_tag'] ?? '') }}" required>
            @error('asset_tag')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Device Type (Dropdown with Search) -->
        <div class="form-group mb-3">
            <label for="device_type_id">
                <i class="fas fa-desktop"></i> Device Type
            </label>
            <select name="device_type_id" id="device_type_id" class="form-control select2" required>
                <option value="" disabled selected>Select a Device Type</option>
                @foreach($deviceTypes as $deviceType)
                    <option value="{{ $deviceType->id }}" {{ old('device_type_id', $asset['device_type_id'] ?? '') == $deviceType->id ? 'selected' : '' }}>
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
            <select name="brand_id" id="brand_id" class="select2 form-control" required>
                <option value="" disabled selected>Select a Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $asset['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>
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
            <input type="text" name="model" class="form-control" value="{{ old('model', $asset['model'] ?? '') }}">
        </div>

        <!-- Serial Number -->
        <div class="form-group mb-3">
            <label for="serial_no">
                <i class="fas fa-barcode"></i> Serial Number
            </label>
            <input type="text" name="serial_no" class="form-control" value="{{ old('serial_no', $asset['serial_no'] ?? '') }}">
        </div>

        <!-- Condition -->
        <div class="form-group mb-3">
            <label for="condition">
                <i class="fas fa-clipboard-check"></i> Condition
            </label>
            <select name="condition" class="form-control">
                <option value="new" {{ old('condition', $asset['condition'] ?? '') == 'new' ? 'selected' : '' }}>New</option>
                <option value="moderate" {{ old('condition', $asset['condition'] ?? '') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                <option value="poor" {{ old('condition', $asset['condition'] ?? '') == 'poor' ? 'selected' : '' }}>Poor</option>
                <option value="defective" {{ old('condition', $asset['condition'] ?? '') == 'defective' ? 'selected' : '' }}>Defective</option>
                <option value="for repair" {{ old('condition', $asset['condition'] ?? '') == 'for repair' ? 'selected' : '' }}>For Repair</option>
            </select>
        </div>

        <!-- Status -->
        <div class="form-group mb-3">
            <label for="status">
                <i class="fas fa-info-circle"></i> Status
            </label>
            <select name="status" class="form-control">
                <option value="available" {{ old('status', $asset['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="issued" {{ old('status', $asset['status'] ?? '') == 'issued' ? 'selected' : '' }}>Issued</option>
            </select>
        </div>

        <!-- Company (Dropdown) -->
        <div class="form-group mb-3">
            <label for="company_id">
                <i class="fas fa-building"></i> Company
            </label>
            @if(auth()->user()->role !== 'admin')
                <!-- Only show the manager's company, disable the dropdown -->
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                <input type="text" class="form-control" value="{{ auth()->user()->company->name }}" disabled>
            @else
                <!-- Show dropdown for other roles -->
                <select name="company_id" class="form-control" required>
                    <option value="" disabled selected>Select a Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $asset['company_id'] ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- Note -->
        <div class="form-group mb-3">
            <label for="note">
                <i class="fas fa-info-circle"></i> Note
            </label>
            <textarea name="note" class="form-control">{{ old('note', $asset['note'] ?? '') }}</textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Asset
        </button>
    </form>
</div>

@endsection
