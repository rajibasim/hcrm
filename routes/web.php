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
        Route::resource('users', UserController::class);
    });

    ### master data start
    Route::group(['prefix' => '/admin/masterdata'], function () {
        // unit Master
        Route::resource('unit', UnitController::class);
        // product Master
        Route::resource('product', ProductController::class);
        // beat Master
        Route::resource('beat', BeatController::class);
        // area Master
        Route::resource('area', AreaController::class);
        // customer Master
        Route::resource('customer', CustomerController::class);
    });
    ### master data end
    ### user level & permission
    Route::group(['prefix' => '/admin/usersrole'], function () {
        // user route
        Route::resource('user', App\Http\Controllers\Admin\user\UserController::class);
        // sales person route
        //Route::resource('user', App\Http\Controllers\Admin\user\UserController::class);
        // role route
        Route::resource('role', App\Http\Controllers\Admin\user\RoleController::class);
    });
    ### user level & permission
    ### park
    Route::group(['prefix' => '/admin/adminpark'], function () {
        // Park Master
        Route::resource('parks', ParkController::class);
        // Park employee Master
        Route::resource('employee', EmployeeController::class);
        // Park gallery Master
        Route::resource('gallery', GalleryController::class);
        // Park notice Master
        Route::resource('notice', NoticeController::class);
        // Park activity
        Route::resource('park_activity', ParkActivityController::class);
        // Park service
        Route::resource('park_service', ParkServiceController::class);
        // Park Gate
        Route::resource('park_gate', ParkGateController::class);
         // Park Gate
        Route::resource('park_entry', ParkEntryController::class);
    });
    ### park
     ### park
    Route::group(['prefix' => '/admin/adminbooking'], function () {
        // Ticket Booking
        Route::resource('ticket', TicketController::class);
        Route::get('ticket/print/{id}', [TicketController::class, 'print'])->name('print');
        // Customer
        Route::resource('customer', CustomerController::class);
    });
    ### park
});



//Auth::routes();

Route::get('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('login');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
