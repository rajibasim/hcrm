<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Web Routes For Admin
|--------------------------------------------------------------------------
*/
  
Route::get('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('login');
Route::post('admin/post-login', [App\Http\Controllers\Admin\LoginController::class, 'processLogin'])->name('login.post'); 
Route::get('admin/logout', [App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function() {
    Route::get('admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    ### master data start
    Route::group(['prefix' => '/admin/masterdata'], function () {
        // unit Master
        Route::resource('unit', App\Http\Controllers\Admin\masterdata\UnitController::class);
        // category Master
        Route::resource('status', App\Http\Controllers\Admin\masterdata\StatusController::class);
        // Beat Master 
        // Sales person Master
        Route::resource('sales-person', App\Http\Controllers\Admin\masterdata\SalesPersonController::class);      
    });
    ### master data end
    ### user level & permission & 
    Route::group(['prefix' => '/admin/usersrole'], function () {
        // user route
        Route::resource('user', App\Http\Controllers\Admin\user\UserController::class);
        // role route
        Route::resource('role', App\Http\Controllers\Admin\user\RoleController::class);
    });
    ### user level & permission
   
    ### Customer
    Route::group(['prefix' => '/admin/managecustomer'], function () {
        // customer
        Route::resource('customer', App\Http\Controllers\Admin\customer\CustomerController::class);
    });
    ### 

    ### Bill Entry
    Route::group(['prefix' => '/admin/managebill'], function () {
        // Bill Entry
        Route::resource('bill', App\Http\Controllers\Admin\bill\BillEntryController::class);
        Route::resource('bil-payment-history', App\Http\Controllers\Admin\bill\PaymentHistoryController::class);
    });
    ### Bill Entry

    ### Balance Sheet
    Route::group(['prefix' => '/admin/managebalancesheet'], function () {
        // Balance Sheet
        Route::resource('balance-sheet', App\Http\Controllers\Admin\balancesheet\BalanceSheetController::class);
        //Inventory purchase
        Route::resource('inventory-history', App\Http\Controllers\Admin\balancesheet\InventoryHistoryController::class);
        //Inventory billed
        Route::resource('inventory-billed', App\Http\Controllers\Admin\balancesheet\InventoryBilledController::class);
        // Balance Transfer
        Route::resource('balance-transfer', App\Http\Controllers\Admin\balancesheet\BalanceTransferController::class);
        // Balance Report
        Route::resource('payment-history', App\Http\Controllers\Admin\balancesheet\PaymentHistoryController::class);
        // Credit Report
        Route::resource('credit-report', App\Http\Controllers\Admin\balancesheet\CreditReportController::class);
        // Credit Report
        Route::resource('credit-history', App\Http\Controllers\Admin\balancesheet\CreditHistoryController::class);
        // Expenditure History
        Route::resource('expenditure-history', App\Http\Controllers\Admin\balancesheet\ExpenditureHistoryController::class);
    });
    ### Balance Sheet
});

