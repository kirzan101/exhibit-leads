<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OpcLeadResource;
use App\Models\OpcLead;
use App\Services\OpcLeadService;
use Exception;
use Illuminate\Support\Facades\Auth;

class OpcLeadController extends Controller
{
    public function __construct(private OpcLeadService $opcLeadService)
    {
        $this->opcLeadService = $opcLeadService;
    }

    public function storeBulk(Request $request)
    {
        $response_code = 422;

        if ($request->has('leads')) {
            $result = $this->opcLeadService->createBulkOpcLead($request->toArray());
            $response_code = ($result['result'] == 'success') ? 200 : 500;
        }

        return response()->json([
            'message' => $result['message'],
        ], $response_code);
    }
}
