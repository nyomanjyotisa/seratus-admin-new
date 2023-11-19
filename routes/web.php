<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\OtherIncome;
use App\Models\Permission;
use App\Models\Role;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'users'         => (int) User::count(),
        'roles'         => (int) Role::count(),
        'permissions'   => (int) Permission::count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale); 
    return back();
})->name('setlang');

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

    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');
 
    Route::get('/report/{year}/{month}', [TransactionController::class, 'report'])->name('report');
    Route::get('/report/{year}/{month}/download', [TransactionController::class, 'report'])->name('report.download');
});

require __DIR__.'/auth.php';
