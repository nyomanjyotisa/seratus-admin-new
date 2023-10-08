<?php

namespace App\Http\Controllers;

use App\Http\Requests\Production\ProductionStoreRequest;
use App\Http\Requests\Sale\SaleUpdateRequest;
use App\Http\Requests\Transaction\TransactionIndexRequest;
use App\Models\Expense;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionIndexRequest $request)
    {
        $expenses = Expense::query();
        $expenses->where('transaction_id', '=', NULL);
        if ($request->has('search')) {
            $expenses->where('description', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $expenses->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $roles = Role::get();
        return Inertia::render('Expense/Index', [
            'title'         => 'Expense',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'roles'         => $roles,
            'expenses'      => $expenses->paginate($perPage),
            'breadcrumbs'   => [['label' => 'Expense', 'href' => route('expense.index')]],
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
            $expense = Expense::create([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $expense->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Expense']) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(SaleUpdateRequest $request, Expense $expense)
    {
        DB::beginTransaction();
        try {
            $expense->update([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $expense->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Expense']) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $expense->description]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $expense->description]) . $th->getMessage());
        }
    }
}
