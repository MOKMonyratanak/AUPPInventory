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
        try {
            $user = auth()->user();
            $companyId = $request->query('company_id'); // Get the selected company ID from the request
    
            // If the user is a manager, restrict to their company
            if ($user->role === 'manager') {
                $companyId = $user->company_id;
            }
    
            // Filter assets and activities based on the selected company (or all companies if none selected)
            $totalAssetsQuery = Asset::query();
            $issuedAssetsQuery = Asset::query();
            $deviceDataQuery = Asset::query();
            $brandDataQuery = Asset::query();
            $recentActivitiesQuery = ActivityLog::query();
    
            if ($companyId) {
                $totalAssetsQuery->where('company_id', $companyId);
                $issuedAssetsQuery->where('company_id', $companyId);
                $deviceDataQuery->where('company_id', $companyId);
                $brandDataQuery->where('company_id', $companyId);
                $recentActivitiesQuery->whereHas('performedBy', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                });
            }
    
            $totalAssets = $totalAssetsQuery->count();
            $issuedAssets = $issuedAssetsQuery->where('status', 'issued')->count();
            $remainingAssets = $totalAssets - $issuedAssets;
    
            $deviceData = $deviceDataQuery->selectRaw(
                'device_type_id, COUNT(*) as total_count, 
                SUM(CASE WHEN status = "issued" THEN 1 ELSE 0 END) as issued_count'
            )
                ->groupBy('device_type_id')
                ->with('deviceType')
                ->get();
    
            $brandData = $brandDataQuery->selectRaw('brand_id, COUNT(*) as brand_count')
                ->groupBy('brand_id')
                ->with('brand')
                ->get();
    
            $recentActivities = $recentActivitiesQuery->latest()
                ->with(['performedBy', 'affectedUser'])
                ->paginate(20);
    
            // Prepare data for charts
            $deviceTypeLabels = [];
            $issuedDeviceCounts = [];
            $remainingDeviceCounts = [];
            foreach ($deviceData as $data) {
                $deviceTypeLabels[] = $data->deviceType->name ?? 'Unknown';
                $issuedDeviceCounts[] = $data->issued_count;
                $remainingDeviceCounts[] = $data->total_count - $data->issued_count;
            }
    
            $brandLabels = [];
            $brandCounts = [];
            foreach ($brandData as $data) {
                $brandLabels[] = $data->brand->name ?? 'Unknown';
                $brandCounts[] = $data->brand_count;
            }
    
            // Fetch all companies for the dropdown (only for admins)
            $companies = $user->role === 'admin' ? \App\Models\Company::all() : [];
    
            return view('dashboard', compact(
                'totalAssets',
                'issuedAssets',
                'remainingAssets',
                'deviceTypeLabels',
                'issuedDeviceCounts',
                'remainingDeviceCounts',
                'recentActivities',
                'brandLabels',
                'brandCounts',
                'companies',
                'companyId'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}