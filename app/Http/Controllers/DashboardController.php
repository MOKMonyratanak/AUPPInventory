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
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Unauthorized access.');
        }

        // Total, issued, and remaining assets
        $totalAssets = Asset::count();
        $issuedAssets = Asset::where('status', 'issued')->count();
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
    
        // Fetch brand distribution
        $brandData = Asset::selectRaw('brand_id, COUNT(*) as brand_count')
            ->groupBy('brand_id')
            ->with('brand')
            ->get();
    
        $brandLabels = [];
        $brandCounts = [];
    
        foreach ($brandData as $data) {
            $brandLabels[] = $data->brand->name ?? 'Unknown';
            $brandCounts[] = $data->brand_count;
        }
    
        // Paginate recent activities
        $recentActivities = ActivityLog::latest()
            ->with(['performedBy', 'affectedUser'])
            ->paginate(10); // 10 items per page
    
        return view('dashboard', compact(
            'totalAssets',
            'issuedAssets',
            'remainingAssets',
            'deviceTypeLabels',
            'issuedDeviceCounts',
            'remainingDeviceCounts',
            'recentActivities',
            'brandLabels',
            'brandCounts'
        ));
    }
    
    
    
    
    
    
}
