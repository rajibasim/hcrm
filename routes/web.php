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
    });
    ### Bill Entry

    ### Balance Sheet
    Route::group(['prefix' => '/admin/managebalancesheet'], function () {
        // Balance Sheet
        Route::resource('balance-sheet', App\Http\Controllers\Admin\balancesheet\BalanceSheetController::class);
    });
    ### Balance Sheet
});

