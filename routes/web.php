<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PriceCalculatorController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GlobalDataController;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    $transactions_for_calc = Transaction::with(['sales', 'otherIncomes', 'expenses', 'productions'])
        ->where('status', 'done')
        ->get();

    $total_pemasukan = 0;

    $total_pengeluaran = 0;

    foreach($transactions_for_calc as $transaction){
        $salesTotal = $transaction->sales->sum('amount');
        $otherIncomesTotal = $transaction->otherIncomes->sum('amount');
        $expensesTotal = $transaction->expenses->sum('amount');
        $productionsTotal = $transaction->productions->sum('amount');

        $total_pemasukan += $salesTotal + $otherIncomesTotal;
        $total_pengeluaran += $expensesTotal + $productionsTotal;
    }

    $expenses_for_calc = Expense::where('transaction_id', '=', NULL)->sum('amount');
    $otherIncomes_for_calc = OtherIncome::where('transaction_id', '=', NULL)->sum('amount');

    $total_pemasukan += $otherIncomes_for_calc;
    $total_pengeluaran += $expenses_for_calc;

    $laba = $total_pemasukan - $total_pengeluaran;

    $saldomasuk = Saldo::where('type', 'masuk')->sum('amount');
    $saldokeluar = Saldo::where('type', 'keluar')->sum('amount');

    $sisasaldo = $saldomasuk - $saldokeluar;

    return Inertia::render('Dashboard', [
        'transaksiPending'          => Transaction::where('status', 'pending')->count(),
        'transaksiSelesai'          => Transaction::where('status', 'done')->count(),
        'total_pemasukan'           => $total_pemasukan,
        'total_pengeluaran'         => $total_pengeluaran,
        'laba'                      => $laba,
        'saldo'                      => $sisasaldo,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale); 
    return back();
})->name('setlang');

Route::get('/transaction/fetch-email', [TransactionController::class, 'fetchEmail'])->name('transaction.fetch-email');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    
    Route::resource('/transaction', TransactionController::class)->except('create', 'edit');
    Route::get('/transaction/list/done', [TransactionController::class, 'indexDone'])->name('transaction.index.done');
    Route::post('/transaction/destroy-bulk', [TransactionController::class, 'destroyBulk'])->name('transaction.destroy-bulk');
    
    Route::resource('/sale', SaleController::class)->except('create', 'edit');
    Route::resource('/production', ProductionController::class)->except('create', 'edit');
    Route::resource('/expense', ExpenseController::class)->except('create', 'edit');
    Route::resource('/other-income', OtherIncomeController::class)->except('create', 'edit');
    Route::resource('/kas', SaldoController::class)->except('create', 'edit');
    Route::post('/kas/tambah-kas/{type}', [SaldoController::class, 'tambahKas'])->name('kas.tambahKas');

    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');
 
    Route::get('/report/{year}/{month}', [TransactionController::class, 'report'])->name('report');
    Route::get('/report/{year}/{month}/download', [TransactionController::class, 'downloadReport'])->name('report.download');

    Route::get('/calculator', [PriceCalculatorController::class, 'index'])->name('calculator.index');

    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting/update', [SettingController::class, 'update'])->name('setting.update');

    Route::get('/global-data', [GlobalDataController::class, 'get'])->name('globaldata.get');
    Route::post('/global-data/update', [GlobalDataController::class, 'update'])->name('globaldata.update');

});

require __DIR__.'/auth.php';
