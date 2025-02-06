<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionIndexRequest;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Http\Requests\Transaction\TransactionUpdateRequest;
use App\Http\Services\FetchEmailService;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Saldo;
use App\Models\Transaction;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TransactionController extends Controller
{

    protected $fetchEmailService;

    public function __construct(FetchEmailService $fetchEmailService)
    {
        $this->fetchEmailService = $fetchEmailService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionIndexRequest $request)
    {
        $transactions = Transaction::query();
        $transactions->where('status', 'pending');
        if ($request->has('search')) {
            $transactions->where(function($query) use ($request) {
                $query->where('unique_code', 'LIKE', "%" . $request->search . "%")
                      ->orWhere('description', 'LIKE', "%" . $request->search . "%")
                      ->orWhere('source', 'LIKE', "%" . $request->search . "%");
            });
        }
        if ($request->has(['field', 'order'])) {
            $transactions->orderBy($request->field, $request->order);
        }else{
            $transactions->orderBy('id', 'DESC');
        }
        $perPage = $request->has('perPage') ? $request->perPage : 20;
        $roles = Role::get();
        return Inertia::render('Transaction/Index', [
            'title'         => 'Transaksi',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'transactions'  => $transactions->paginate($perPage),
            'roles'         => $roles,
            'breadcrumbs'   => [['label' => 'Transaksi', 'href' => route('transaction.index')]],
            'isDone'        => false,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDone(TransactionIndexRequest $request)
    {
        $transactions = Transaction::query();
        $transactions->where('status', 'done');
        if ($request->has('search')) {
            $transactions->where(function($query) use ($request) {
                $query->where('unique_code', 'LIKE', "%" . $request->search . "%")
                      ->orWhere('description', 'LIKE', "%" . $request->search . "%")
                      ->orWhere('source', 'LIKE', "%" . $request->search . "%");
            });
        }
        if ($request->has(['field', 'order'])) {
            $transactions->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 20;
        $roles = Role::get();
        return Inertia::render('Transaction/Index', [
            'title'         => 'Transaksi list Done',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'transactions'  => $transactions->paginate($perPage),
            'roles'         => $roles,
            'breadcrumbs'   => [['label' => 'Transaksi Done', 'href' => route('transaction.index.done')]],
            'isDone'        => true,
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
            return Redirect::route('transaction.show', ['transaction'=>$transaction])->with('success', __('app.label.created_successfully', ['name' => $transaction->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => 'Transaksi']) . $th->getMessage());
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
                'closed_at'         => $request->closed_at,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $transaction->description]));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => 'Transaksi']) . $th->getMessage());
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
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' Transaksi']));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' Transaksi']) . $th->getMessage());
        }
    }

    public function report(Request $request, $year, $month){

        $perPage = $request->has('perPage') ? $request->perPage : 200;
        $date = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-15');
        
        $transactions = Transaction::query();
        $transactions->whereMonth('closed_at', $date->month)->whereYear('closed_at', $date->year);
        
        $expenses = Expense::query();
        $expenses->where('transaction_id', '=', NULL);
        $expenses->whereMonth('date', $date->month)->whereYear('date', $date->year);

        // dd($expenses->data);

        $otherIncomes = OtherIncome::query();
        $otherIncomes->where('transaction_id', '=', NULL);
        $otherIncomes->whereMonth('date', $date->month)->whereYear('date', $date->year);

        $roles = Role::get();

        $transactions_for_calc = Transaction::whereMonth('closed_at', $date->month)->whereYear('closed_at', $date->year)->get();

        $total_pemasukan = 0;

        $total_pengeluaran = 0;

        foreach($transactions_for_calc as $transaction){
            $total_pemasukan += $transaction->sales_total;
            $total_pemasukan += $transaction->other_incomes_total;

            $total_pengeluaran += $transaction->expenses_total;
            $total_pengeluaran += $transaction->productions_total;
        }

        
        $expenses_for_calc = Expense::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('transaction_id', '=', NULL)->sum('amount');
        $otherIncomes_for_calc = OtherIncome::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('transaction_id', '=', NULL)->sum('amount');

        $total_pemasukan += $otherIncomes_for_calc;
        $total_pengeluaran += $expenses_for_calc;

        $laba = $total_pemasukan - $total_pengeluaran;

        if ($total_pemasukan > 0) {
            $persentase_laba_total = ($laba / $total_pemasukan) * 100;
        } else {
            $persentase_laba_total = 0;
        }

        $saldomasuk = Saldo::where('type', 'masuk')->sum('amount');
        $saldokeluar = Saldo::where('type', 'keluar')->sum('amount');
    
        $sisasaldo = $saldomasuk - $saldokeluar;

        return Inertia::render('Report/Index', [
            'title'         => 'Laporan',
            'filters'       => $request->all(['search', 'field', 'order']),
            'perPage'       => (int) $perPage,
            'transactions'  => $transactions->paginate($perPage),
            'expenses'      => $expenses->paginate($perPage),
            'otherIncomes'  => $otherIncomes->paginate($perPage),
            'roles'         => $roles,
            'breadcrumbs'   => [['label' => 'Laporan', 'href' => route('report', [ 'year' => $year, 'month' => $month])]],
            'isDone'        => false,
            'year'          => $year,
            'month'         => $month,
            'total_pemasukan'         => $total_pemasukan,
            'total_pengeluaran'       => $total_pengeluaran,
            'laba'          => $laba,
            'persentase_laba_total'          => $persentase_laba_total,
            'saldo'         => $sisasaldo,
        ]);
    }

    public function fetchEmail(){
        $this->fetchEmailService->fetchTokped();
        $this->fetchEmailService->fetchEtsy();
        $this->fetchEmailService->fetchShopee();
    }

    public function downloadReport(Request $request, $year, $month){
        $date = Carbon::createFromFormat('Y-m-d', $year . '-' . $month . '-15');

        // Get transactions for the specified month and year
        $transactions = Transaction::whereMonth('closed_at', $date->month)
                                    ->whereYear('closed_at', $date->year)
                                    ->with(['sales', 'otherIncomes', 'expenses', 'productions']) // Eager load sales, otherIncomes, and expenses
                                    ->get();

        // Get expenses for the specified month and year
        $expenses = Expense::where('transaction_id', '=', NULL)
                            ->whereMonth('date', $date->month)
                            ->whereYear('date', $date->year)
                            ->get();

        // Get other incomes for the specified month and year
        $otherIncomes = OtherIncome::where('transaction_id', '=', NULL)
                                    ->whereMonth('date', $date->month)
                                    ->whereYear('date', $date->year)
                                    ->get();

        // Calculate totals
        $total_pemasukan = $transactions->sum('sales_total') + $otherIncomes->sum('amount');
        $total_pengeluaran = $transactions->sum('expenses_total')+ $transactions->sum('productions_total') + $expenses->sum('amount');
        $laba = $total_pemasukan - $total_pengeluaran;

        if ($total_pemasukan > 0) {
            $persentase_laba_total = ($laba / $total_pemasukan) * 100;
        } else {
            $persentase_laba_total = 0;
        }

        // Load the PDF view with the data
        $pdf = PDF::loadView('transaction', [
            'transactions' => $transactions,
            'expenses' => $expenses,
            'otherIncomes' => $otherIncomes,
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
            'laba' => $laba,
            'persentase_laba_total' => $persentase_laba_total,
            'year' => $year,
            'month' => $month,
        ]);
        $pdf->setPaper('A4', 'landscape');
        // Download the PDF
        return $pdf->download('report_' . $year . '_' . $month . '.pdf');
    }

}
