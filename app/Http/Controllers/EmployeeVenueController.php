<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeVenueFormRequest;
use App\Services\EmployeeVenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeVenueController extends Controller
{
    private EmployeeVenueService $employeeVenueService;

    public function __construct(EmployeeVenueService $employeeVenueService)
    {
        $this->employeeVenueService = $employeeVenueService;
    }

    /**
     * update current user venues ids
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $request->merge(['employee_id' => Auth::user()->employee->id]);
        
        ['result' => $result, 'message' => $message] = $this->employeeVenueService->updateMultipleEmployeeVenue($request->toArray());

        return redirect()->route('confirms')->with($result, $message);
    }
}
