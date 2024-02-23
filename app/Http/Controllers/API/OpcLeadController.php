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

    /**
     * Store bulk leads
     *
     * @param Request $request
     */
    public function storeBulk(Request $request)
    {
        $response_code = 422;
        $message = 'Unprocessable Content';

        if ($request->has('leads')) {
            $result = $this->opcLeadService->createBulkOpcLead($request->toArray());
            $response_code = ($result['result'] == 'success') ? 200 : 500;
            $message = $result['message'];
        }

        return response()->json([
            'message' => $message,
        ], $response_code);
    }

    /**
     * Store OPC lead
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $response_code = 422;
        $message = 'Unprocessable Content';

        if (true) {
            $result = $this->opcLeadService->createOpcLead($request->toArray());
            $response_code = ($result['result'] == 'success') ? 200 : 500;
            $message = $result['message'];
        }

        return response()->json([
            'message' => $message,
        ], $response_code);
    }

    /**
     * Update OPC lead
     *
     * @param Request $request
     * @param [type] $id
     */
    public function update(Request $request, $id)
    {
        $response_code = 422;
        $message = 'Unprocessable Content';

        if (true) {
            $result = $this->opcLeadService->updateOpcLeadService($request->toArray(), OpcLead::find($id));
            $response_code = ($result['result'] == 'success') ? 200 : 500;
            $message = $result['message'];
        }

        return response()->json([
            'message' => $message,
        ], $response_code);
    }

    /**
     * Delete OPC lead
     *
     * @param [type] $id
     */
    public function destroy($id)
    {
        $response_code = 422;
        $message = 'Unprocessable Content';

        if (true) {
            $result = $this->opcLeadService->deleteOpcLeadService(OpcLead::find($id));
            $response_code = ($result['result'] == 'success') ? 200 : 500;
            $message = $result['message'];
        }

        return response()->json([
            'message' => $message,
        ], $response_code);
    }
}
