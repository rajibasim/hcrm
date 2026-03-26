<?php
  
namespace App\Http\Controllers\Admin;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\BalanceSheetTransactions;
use App\Models\PaymentHistory;
  
class DashboardController extends Controller{
   
    ### Dashboard
    public function index(){
        $metadata = array(
            'page_title' => 'Dashboard'
        );
        if(Auth::check()){
            $customer = Customer::where('deleted_at', '=', NULL)->count();
            $txnDetails = BalanceSheetTransactions::where('is_active', 1)->where('deleted_at', '=', NULL)->latest()->first();

            $billData = new \stdClass();
            $response =  Bill::where('deleted_at', '=', NULL)->where('is_active', 1);
            $sales_person_id = intval(auth()->user()->sales_person_id);
            if(isset($sales_person_id) && intval($sales_person_id) > 0){
                $response->where('sales_person_id', $sales_person_id);
            }
            $billData->billed_amount = $response->sum('billed_amount');
            $billData->damage_amount = $response->sum('damage_amount');
            $billData->return_amount = $response->sum('return_amount');
            $billData->adjusment_amount = $response->sum('adjusment_amount');
            $online_amount = $response->sum('online_amount');
            $cash_amount = $response->sum('cash_amount');
            $billData->paid_amount = $online_amount + $cash_amount;
            $billData->balance_amount = $response->sum('balance_amount');
            $billData->online_amount = $online_amount;
            $billData->cash_amount = $cash_amount;
            $billData->total_number_of_bill = $response->count();
            $paymentRequest = PaymentHistory::where('is_active', '0')->count();
            return view('admin.pages.dashboard.dashboard', compact('metadata', 'customer', 'txnDetails', 'billData', 'paymentRequest'));
        }
        $error_responce = array(
            'status' => 'error',
            'message' => 'Invalid Login Credentials.',
        );
        Session::put('flash_data', $error_responce);
        return redirect("admin/login");
    }
}