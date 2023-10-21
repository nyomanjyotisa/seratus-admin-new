<?php

namespace App\Http\Controllers;

use App\Http\Requests\Production\ProductionStoreRequest;
use App\Http\Requests\Sale\SaleUpdateRequest;
use App\Http\Requests\Transaction\TransactionIndexRequest;
use App\Models\OtherIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OtherIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionIndexRequest $request)
    {
        $otherIncomes = OtherIncome::query();
        $otherIncomes->where('transaction_id', '=', NULL);
        if ($request->has('search')) {
            $otherIncomes->where('description', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $otherIncomes->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('OtherIncome/Index', [
            'title'         => 'Other Income',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'otherIncomes'  => $otherIncomes->paginate($perPage),
            'breadcrumbs'   => [['label' => 'Other Income', 'href' => route('other-income.index')]],
        ]);
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
            $otherIncome = OtherIncome::create([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $otherIncome->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Other Income']) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OtherIncome  $otherIncome
     * @return \Illuminate\Http\Response
     */
    public function show(OtherIncome $otherIncome)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OtherIncome  $otherIncome
     * @return \Illuminate\Http\Response
     */
    public function edit(OtherIncome $otherIncome)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OtherIncome  $otherIncome
     * @return \Illuminate\Http\Response
     */
    public function update(SaleUpdateRequest $request, OtherIncome $otherIncome)
    {
        DB::beginTransaction();
        try {
            $otherIncome->update([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $otherIncome->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Other Income']) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OtherIncome  $otherIncome
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtherIncome $otherIncome)
    {
        try {
            $otherIncome->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $otherIncome->description]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $otherIncome->description]) . $th->getMessage());
        }
    }
}
