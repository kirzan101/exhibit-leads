<?php

namespace App\Http\Controllers;

use App\Exports\ConfirmedsExport;
use App\Http\Resources\ExportConfirmedResource;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Download Confirmed reports
     *
     * @param Request $request
     * @return void
     */
    public function confirmedReport(Request $request)
    {
        $leads = $this->reportService->confirmedReport($request->toArray());
        $leads_resource = ExportConfirmedResource::collection($leads);

        $file_name = sprintf('%s-%s.xlsx', Carbon::now()->format('Y-m-d-H-i'), 'confirmed');
        $exported_leads = new ConfirmedsExport($leads_resource->resource->toArray());

        return ($exported_leads)->download($file_name);
 
    }
}
