<?php

namespace App\Services;

use App\Models\OpcLead;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OpcLeadService
{
    public $last_id = null;
    public $module_name = 'opc_leads';

    /**
     * Get all the OPC Leads service
     *
     * @return Collection
     */
    public function indexOpcLead(): Collection
    {
        $opc_leads = OpcLead::orderBy('id', 'desc')->get();

        return $opc_leads;
    }

    /**
     * Get all the Paginated OPC Leads service
     *
     * @return Paginator
     */
    public function indexPaginateOpcLead(array $request): Paginator
    {
        $opc_leads = OpcLead::query();

        //set default values
        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $opc_leads->where(function ($query) use ($request) {
                $query->where('first_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orwhere('companion_first_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('companion_last_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('occupation', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('mobile_number', 'LIKE', '%' . $request['search'] . '%');
            });
        }

        return $opc_leads->orderBy($sort_by, $sort)->paginate($per_page);;
    }

    /**
     * create opc leads service
     *
     * @param array $request
     * @return array
     */
    public function createBulkOpcLead(array $request): array
    {
        try {
            DB::beginTransaction();

            if ($request['leads']) {
                $leads = json_decode($request['leads']);

                foreach ($leads as $lead) {
                    $opc_leads = OpcLead::where('first_name', $lead->first_name)->where('last_name', $lead->last_name)->get();

                    if ($opc_leads->count() == 0) {
                        $opc_lead = OpcLead::create([
                            'first_name' => $lead->first_name,
                            'middle_name' => $lead->middle_name,
                            'last_name' => $lead->last_name,
                            'companion_first_name' => $lead->companion_first_name,
                            'companion_middle_name' => $lead->companion_middle_name,
                            'companion_last_name' => $lead->companion_last_name,
                            'address' => $lead->address,
                            'hotel' => $lead->hotel,
                            'mobile_number' => $lead->mobile_number,
                            'occupation' => $lead->occupation,
                            'age' => $lead->age,
                            'source' => $lead->source,
                            'source_prefix' => $lead->source_prefix,
                            'civil_status' => $lead->civil_status,
                            'remarks' => $lead->remarks,
                            'date_filled' => Carbon::parse($lead->created_at)->format('Y-m-d'),
                        ]);
                    }
                }
            }

            $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * create opc leads service
     *
     * @param array $request
     * @return array
     */
    public function createOpcLead(array $request): array
    {
        try {
            DB::beginTransaction();

            $opc_lead = OpcLead::create([
                'first_name' => $request['first_name'],
                'middle_name' => $request['middle_name'],
                'last_name' => $request['last_name'],
                'companion_first_name' => $request['companion_first_name'],
                'companion_middle_name' => $request['companion_middle_name'],
                'companion_last_name' => $request['companion_last_name'],
                'address' => $request['address'],
                'hotel' => $request['hotel'],
                'mobile_number' => $request['mobile_number'],
                'occupation' => $request['occupation'],
                'age' => $request['age'],
                'source' => $request['source'],
                'source_prefix' => $request['source_prefix'],
                'civil_status' => $request['civil_status'],
                'remarks' => $request['remarks'],
                'date_filled' => Carbon::parse($request['created_at'])->format('Y-m-d'),
            ]);

            $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * Update opc lead details
     *
     * @param array $request
     * @param OpcLead $opcLead
     * @return array
     */
    public function updateOpcLeadService(array $request, OpcLead $opcLead): array
    {
        try {
            $opcLead = tap($opcLead)->update([
                'first_name' => $request['first_name'],
                'middle_name' => $request['middle_name'],
                'last_name' => $request['last_name'],
                'companion_first_name' => $request['companion_first_name'],
                'companion_middle_name' => $request['companion_middle_name'],
                'companion_last_name' => $request['companion_last_name'],
                'address' => $request['address'],
                'hotel' => $request['hotel'],
                'mobile_number' => $request['mobile_number'],
                'occupation' => $request['occupation'],
                'age' => $request['age'],
                'source' => $request['source'],
                'source_prefix' => $request['source_prefix'],
                'civil_status' => $request['civil_status'],
                'remarks' => $request['remarks'],
                'date_filled' => Carbon::parse($request['created_at'])->format('Y-m-d')
            ]);

            $this->last_id = $opcLead->getKey();

            $return_values = ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        return $return_values;
    }

    /**
     * Show OPC Lead details
     *
     * @param OpcLead $model
     * @return OpcLead
     */
    public function showOpcLead(OpcLead $model): OpcLead
    {
        $opcLead = new OpcLead;
        $opcLead = $model;

        return $opcLead;
    }

    /**
     * delete OPC lead service
     *
     * @param OpcLead $opcLead
     * @return array
     */
    public function deleteOpcLeadService(OpcLead $opcLead): array
    {
        $this->last_id = $opcLead->id;

        try {
            $opcLead->delete();

            $return_values = ['result' => 'success', 'message' => 'Successfully deleted!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        return $return_values;
    }
}
