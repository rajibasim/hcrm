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
        Route::resource('category', App\Http\Controllers\Admin\masterdata\CategoryController::class);
        // Beat Master
        Route::resource('beat', App\Http\Controllers\Admin\masterdata\BeatController::class);
        // Area Master
        Route::resource('area', App\Http\Controllers\Admin\masterdata\AreaController::class);
        // Product Master
        Route::resource('product', App\Http\Controllers\Admin\masterdata\ProductController::class);  
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
    ### Customer
    ### Return Entry
    Route::group(['prefix' => '/admin/managereturnentry'], function () {
        // return
        Route::resource('return-entry', App\Http\Controllers\Admin\return_entry\ReturnEntryController::class);
        // Product return
        Route::resource('return-product', App\Http\Controllers\Admin\return_entry\ReturnProductController::class);
    });
    ### Return Entry
});



//Auth::routes();

Route::get('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('login');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
