<?php

namespace App\Services;

use App\Models\Contract;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ContractService
{
    /**
     * index of member service
     *
     * @return Collection
     */
    public function indexContract() : Collection
    {
        $contract = Contract::all();

        return $contract;
    }

    /**
     * create contract service
     *
     * @param array $request
     * @return Contract
     */
    public function createContract(array $request) : Contract
    {
        $contract = Contract::create($request);

        return $contract;
    }
    
    /**
     * update contract service
     *
     * @param array $request
     * @param Contract $contract
     * @return Contract
     */
    public function updateContract(array $request, Contract $contract) : Contract
    {
        $contract = tap($contract)->update($request);

        return $contract;
    }

    public function deleteContract(Contract $contract) : bool
    {
        $contract = $contract->delete();

        return $contract;
    }
}