<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DeviceType;
use App\Models\Asset;
use App\Models\Company;
use Illuminate\Http\Request;

class ReportController extends Controller
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

            // Retrieve the start and end dates from the request
            $startDate = $request->query('start_date', now()->subMonth()->toDateString());
            $endDate = $request->query('end_date', now()->toDateString());

            // Query activity logs and join with assets to get device type
            $logsQuery = ActivityLog::selectRaw('assets.device_type_id, action, COUNT(*) as count')
                ->join('assets', 'activity_logs.asset_tag', '=', 'assets.asset_tag')
                ->whereBetween('activity_logs.created_at', [$startDate, $endDate])
                ->whereIn('action', ['issue', 'return']);

            if ($companyId) {
                $logsQuery->where('assets.company_id', $companyId);
            }

            $logs = $logsQuery->groupBy('assets.device_type_id', 'action')->get();

            // Fetch device type names and organize data for activity logs
            $deviceTypes = DeviceType::pluck('name', 'id');
            $reportData = [];
            foreach ($logs as $log) {
                $deviceTypeName = $deviceTypes[$log->device_type_id] ?? 'Unknown';
                $reportData[$deviceTypeName][$log->action] = $log->count;
            }

            // Query assets to calculate counts for available, issued, and non-issuable devices
            $deviceDataQuery = Asset::selectRaw(
                'device_type_id,
                SUM(CASE WHEN status = "issued" THEN 1 ELSE 0 END) as issued_count,
                SUM(CASE WHEN status = "available" AND `condition` IN ("new", "moderate") THEN 1 ELSE 0 END) as available_count,
                SUM(CASE WHEN status = "available" AND `condition` NOT IN ("new", "moderate") THEN 1 ELSE 0 END) as non_issuable_count'
            )
                ->groupBy('device_type_id')
                ->with('deviceType');

            if ($companyId) {
                $deviceDataQuery->where('company_id', $companyId);
            }

            $deviceData = $deviceDataQuery->get();

            // Organize data for the new functionality
            $deviceTypeReport = [];
            foreach ($deviceData as $data) {
                $deviceTypeName = $data->deviceType->name ?? 'Unknown';
                $deviceTypeReport[$deviceTypeName] = [
                    'available' => $data->available_count,
                    'issued' => $data->issued_count,
                    'non_issuable' => $data->non_issuable_count,
                ];
            }

            // Fetch all companies for the dropdown (only for admins)
            $companies = $user->role === 'admin' ? Company::all() : [];

            return view('reports.index', compact('reportData', 'deviceTypeReport', 'startDate', 'endDate', 'companies', 'companyId'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}