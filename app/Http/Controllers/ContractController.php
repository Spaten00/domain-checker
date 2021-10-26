<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreContractRequest $request
     * @return RedirectResponse
     */
    public function store(StoreContractRequest $request): RedirectResponse
    {
        $contract = Contract::create([
            'customer_id' => $request->customer_id,
            'contract_number' => $request->contract_number,
        ]);

        $contract->domains()->attach($request->domain_id);

        return redirect()->back()->with('status', ['class' => 'success', 'msg' => 'Eintrag wurde erstellt!']);
    }

    /**
     * Display the specified resource.
     *
     * @param Contract $contract
     * @return Response
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contract $contract
     * @return Response
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Contract $contract
     * @return Response
     */
    public function update(Request $request, Contract $contract)
    {
        $contract->contract_number = $request->newNumber;
        $contract->save();
        return response("Vertragsnummer geändert. ");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contract $contract
     * @return Response
     */
    public function destroy(Contract $contract)
    {
        //
    }
}
