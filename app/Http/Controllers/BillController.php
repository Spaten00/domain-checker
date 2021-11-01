<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Models\Bill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBillRequest $request
     * @return RedirectResponse
     */
    public function store(StoreBillRequest $request): RedirectResponse
    {
        Bill::create([
            'contract_id' => $request->contract_id,
            'bill_number' => $request->bill_number,
            'date' => $request->date,
        ]);
        return redirect()->back()->with('status', [
            'msg' => 'Eintrag wurde erstellt!',
            'class' => 'success']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bill $bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bill $bill)
    {
        $bill->bill_number = $request->newNumber;
        $bill->save();
        return response("Letzte Rechnungsnummer geändert. ");
    }
}
