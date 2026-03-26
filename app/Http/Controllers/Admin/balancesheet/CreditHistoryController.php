<?php

namespace App\Http\Controllers\Admin\balancesheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\DeliveryStatus;
use App\Models\SalesPerson;
use App\Models\PaymentHistory;
use App\Models\StatusHistory;
use App\Models\BalanceSheetTransactions;
use Validator;

class CreditHistoryController extends Controller{

    public function __construct(){
        $this->middleware('permission:credit_report_view|credit_report_create|credit_report_edit|credit_report_delete', ['only' => ['index','store']]);
        $this->middleware('permission:credit_report_create', ['only' => ['create','store']]);
        $this->middleware('permission:credit_report_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:credit_report_delete', ['only' => ['destroy']]);
        $this->title = 'Bill Payment History';
        $this->slug = route('credit-history.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = Bill::where('deleted_at', '=', NULL)->with('customer')->with('DeliveryStatus')->with('SalesPerson')->orderBy('id', 'desc');

        $sales_person_id = intval(auth()->user()->sales_person_id);
        if(isset($sales_person_id) && intval($sales_person_id) > 0){
            $response->where('sales_person_id', $sales_person_id);
        }

        // --- Filters --
        if ($request->adjusment_amount) {
            $response->where('adjusment_amount', '>', 0);
            $serach_data['adjusment_amount'] = $request->adjusment_amount;
        }

        if ($request->damage_amount) {
            $response->where('damage_amount', '>', 0);
            $serach_data['damage_amount'] = $request->damage_amount;
        }
        
        if ($request->return_amount) {
            $response->where('return_amount', '>', 0);
            $serach_data['return_amount'] = $request->return_amount;
        }

        if ($request->balance_amount) {
            $response->where('balance_amount', '>', 0);
            $serach_data['balance_amount'] = $request->balance_amount;
        }

        if ($request->filled('sales_person_id')) {
            $response->where('sales_person_id', $request->sales_person_id);
            $serach_data['sales_person_id'] = $request->sales_person_id;
        }

        if ($request->filled('customer_id')) {
            $response->where('customer_id', $request->customer_id);
            $serach_data['customer_id'] = $request->customer_id;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $response->whereBetween('created_at', [$request->start_date, $request->end_date]);
            $serach_data['start_date'] = $request->start_date;
            $serach_data['end_date'] = $request->end_date;
        } elseif ($request->filled('start_date')) {
            $response->whereDate('created_at', '>=', $request->start_date);
            $serach_data['start_date'] = $request->start_date;
        } elseif ($request->filled('end_date')) {
            $response->whereDate('created_at', '<=', $request->end_date);
            $serach_data['end_date'] = $request->end_date;
        }

        $rows = $response->paginate(20);

        $metadata = array(
            'page_title' => $this->title,
            'page_url' => $this->slug,
            'serach_data' => $serach_data,
            'breadcumb' => array(
                array(
                    'url' => '/dashboard',
                    'title' => 'Home',  
                ),
                array(
                    'url' => '',
                    'title' => $this->title,  
                )
            ),
        );

        $SalesPerson = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.credit-history.list', compact('rows', 'metadata', 'SalesPerson'));
    }
}
