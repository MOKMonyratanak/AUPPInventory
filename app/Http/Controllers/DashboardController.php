<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asset;
use App\Models\DeviceType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Existing logic for total and issued assets
        $totalAssets = Asset::count();
        $issuedAssets = Asset::where('status', 'issued')->count();
    
        // New logic for remaining assets
        $remainingAssets = $totalAssets - $issuedAssets;
    
        $deviceData = Asset::selectRaw(
            'device_type_id, COUNT(*) as total_count, 
            SUM(CASE WHEN status = "issued" THEN 1 ELSE 0 END) as issued_count'
        )
            ->groupBy('device_type_id')
            ->with('deviceType')
            ->get();
    
        $deviceTypeLabels = [];
        $issuedDeviceCounts = [];
        $remainingDeviceCounts = [];
    
        foreach ($deviceData as $data) {
            $deviceTypeLabels[] = $data->deviceType->name ?? 'Unknown';
            $issuedDeviceCounts[] = $data->issued_count;
            $remainingDeviceCounts[] = $data->total_count - $data->issued_count;
        }
    
        // New logic: Fetch brand distribution
        $brandData = Asset::selectRaw(
            'brand_id, COUNT(*) as brand_count'
        )
            ->groupBy('brand_id')
            ->with('brand') // Eager load the brand relationship
            ->get();
    
        $brandLabels = [];
        $brandCounts = [];
    
        foreach ($brandData as $data) {
            $brandLabels[] = $data->brand->name ?? 'Unknown';
            $brandCounts[] = $data->brand_count;
        }
    
        // Existing activity filter
        $activityFilter = $request->input('filter', 'last_10');
        $recentActivities = ActivityLog::latest()
            ->with(['user', 'affectedUser'])
            ->when($activityFilter === 'last_10', fn($query) => $query->take(10))
            ->get();
    
        return view('dashboard', compact(
            'totalAssets',
            'issuedAssets',
            'remainingAssets', // Pass remaining assets to the view
            'deviceTypeLabels',
            'issuedDeviceCounts',
            'remainingDeviceCounts',
            'recentActivities',
            'activityFilter',
            'brandLabels',
            'brandCounts'
        ));
    }
    
    
    
    
    
}
