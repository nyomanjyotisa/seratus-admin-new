<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionIndexRequest;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Http\Requests\Transaction\TransactionUpdateRequest;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionIndexRequest $request)
    {
        $transactions = Transaction::query();
        if ($request->has('search')) {
            $transactions->where('unique_code', 'LIKE', "%" . $request->search . "%");
            $transactions->orWhere('description', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $transactions->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $roles = Role::get();
        return Inertia::render('Transaction/Index', [
            'title'         => 'Transaction',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'transactions'  => $transactions->paginate($perPage),
            'roles'         => $roles,
            'breadcrumbs'   => [['label' => 'Transaction', 'href' => route('transaction.index')]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'unique_code' => $request->unique_code,
                'description' => $request->description,
                'source' => $request->source,
                'date' => $request->date,
                'status' => 'pending',
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $transaction->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Transaction']) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        // dd($transaction->sales);
        return Inertia::render('Transaction/Show', [
            'transaction' => $transaction,
            'sales' => $transaction->sales,
            'productions' => $transaction->productions,
            'expenses' => $transaction->expenses,
            'otherIncomes' => $transaction->otherIncomes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionUpdateRequest $request, Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            $transaction->update([
                'unique_code'       => $request->unique_code,
                'description'       => $request->description,
                'status'            => $request->status,
                'source'            => $request->source,
                'date'              => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $transaction->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Transaction']) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $transaction->description]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $transaction->description]) . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $transaction = Transaction::whereIn('id', $request->id);
            $transaction->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' Transaction']));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' Transaction']) . $th->getMessage());
        }
    }
}
