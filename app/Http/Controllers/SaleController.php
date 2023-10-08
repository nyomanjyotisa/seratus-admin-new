<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\SaleStoreRequest;
use App\Http\Requests\Sale\SaleUpdateRequest;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaleStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'transaction_id' => $request->transaction_id,
                'payment_channel' => $request->payment_channel,
                'payment_type' => $request->payment_type,
                'proof_of_payment' => $request->proof_of_payment,
                'amount' => $request->amount,
                'price' => $request->price,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $sale->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Sale']) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(SaleUpdateRequest $request, Sale $sale)
    {
        DB::beginTransaction();
        try {
            $sale->update([
                'transaction_id' => $request->transaction_id,
                'payment_channel' => $request->payment_channel,
                'payment_type' => $request->payment_type,
                'proof_of_payment' => $request->proof_of_payment,
                'amount' => $request->amount,
                'price' => $request->price,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $sale->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Sale']) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        try {
            $sale->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $sale->description]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $sale->description]) . $th->getMessage());
        }
    }
}
