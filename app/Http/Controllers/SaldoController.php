<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionIndexRequest;
use App\Models\Role;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionIndexRequest $request)
    {
        $expenses = Saldo::query();
        if ($request->has('search')) {
            $expenses->where('description', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $expenses->orderBy($request->field, $request->order);
        }else{
            $expenses->orderBy('id', 'DESC');
        }
        $perPage = $request->has('perPage') ? $request->perPage : 20;
        $roles = Role::get();
        return Inertia::render('Saldo/Index', [
            'title'         => 'Kas',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'roles'         => $roles,
            'saldos'      => $expenses->paginate($perPage),
            'breadcrumbs'   => [['label' => 'Kas', 'href' => route('kas.index')]],
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $expense = Saldo::create([
                'amount' => $request->amount,
                'description' => $request->description,
                'type' => $request->type,
                'date' => $request->date,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $expense->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Kas']) . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Saldo  $saldo
     * @return \Illuminate\Http\Response
     */
    public function show(Saldo $saldo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Saldo  $saldo
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Saldo $saldo)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Saldo  $saldo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Saldo $ka)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $ka->update([
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'type' => $request->type,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $ka->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Kas']) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Saldo  $saldo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Saldo $ka)
    {
        try {
            $ka->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $ka->description]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $ka->description]) . $th->getMessage());
        }
    }
}
