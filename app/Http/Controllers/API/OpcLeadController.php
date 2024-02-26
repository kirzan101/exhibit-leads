<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OpcLeadResource;
use App\Models\OpcLead;
use App\Models\User;
use App\Services\OpcLeadService;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthMobileService;

class OpcLeadController extends Controller
{
    public function __construct(private OpcLeadService $opcLeadService, private AuthMobileService $authMobileService)
    {
        $this->opcLeadService = $opcLeadService;
        $this->authMobileService = $authMobileService;
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

    /**
     * login the user
     *
     * @param Request $request
     * @return void
     */
    public function loginMobile(Request $request)
    {
        //decrypt password
        $request->merge(['password' => $this->authMobileService->decryptMobilePassword($request->password)]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            // if account is deactivated
            if (!Auth::user()->is_active) {

                Auth::logout();

                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => [
                        'email' => 'The account is deactivated.'
                    ]
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' => $user,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    /**
     * Encrypt password
     *
     * @param Request $password
     * @return void
     */
    public function encryptMobilePassword(Request $request)
    {
        $encrypted_password = $this->authMobileService->encryptMobilePassword($request->password);

        return $encrypted_password;
    }

    /**
     * Decrypt password
     *
     * @param Request $request
     * @return void
     */
    public function decryptMobilePassword(Request $request)
    {
        $decrypted_password = $this->authMobileService->decryptMobilePassword($request->password);

        return $decrypted_password;
    }
}
