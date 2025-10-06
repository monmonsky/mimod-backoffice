<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index()
    {
        return view('pages.reports.inventory.index');
    }

    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented'
        ]);
    }
}
