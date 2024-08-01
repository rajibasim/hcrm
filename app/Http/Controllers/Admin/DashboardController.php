<?php
  
namespace App\Http\Controllers\Admin;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesPerson;
use App\Models\ReturnEntry;
use App\Models\ReturnEntryProduct;
  
class DashboardController extends Controller{
   
    ### Dashboard
    public function index(){
        $metadata = array(
            'page_title' => 'Dashboard'
        );
        if(Auth::check()){

            $product = Product::where('deleted_at', '=', NULL)->count();
            $customer = Customer::where('deleted_at', '=', NULL)->count();
            $qty = ReturnEntryProduct::where('deleted_at', '=', NULL)->sum('product_qty');
            $amount = ReturnEntryProduct::where('deleted_at', '=', NULL)->sum('sub_total');
            return view('admin.pages.dashboard.dashboard', compact('metadata', 'product', 'customer', 'qty', 'amount'));
        }
        $error_responce = array(
            'status' => 'error',
            'message' => 'Invalid Login Credentials.',
        );
        Session::put('flash_data', $error_responce);
        return redirect("admin/login");
    }
}