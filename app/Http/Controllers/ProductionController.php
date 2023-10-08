<?php

namespace App\Http\Controllers;

use App\Http\Requests\Production\ProductionStoreRequest;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
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
    public function store(ProductionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $production = Production::create([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $production->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Production']) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function show(Production $production)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function edit(Production $production)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Production $production)
    {
        DB::beginTransaction();
        try {
            $production->update([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $production->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Production']) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function destroy(Production $production)
    {
        try {
            $production->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $production->description]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $production->description]) . $th->getMessage());
        }
    }
}
