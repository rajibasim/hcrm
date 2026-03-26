<?php

namespace App\Http\Controllers\Admin\balancesheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BalanceSheets;
use App\Models\Bill;
use Validator;

class CreditReportController extends Controller{

    public function __construct(){
        $this->middleware('permission:credit_report_view|credit_report_create|credit_report_edit|credit_report_delete', ['only' => ['index','store']]);
        $this->middleware('permission:credit_report_create', ['only' => ['create','store']]);
        $this->middleware('permission:credit_report_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:credit_report_delete', ['only' => ['destroy']]);
        $this->title = 'Credit Report';
        $this->slug = route('credit-report.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        //Billed Amount
        $response =  Bill::where('deleted_at', '=', NULL)->where('is_active', 1);

        $sales_person_id = intval(auth()->user()->sales_person_id);
        if(isset($sales_person_id) && intval($sales_person_id) > 0){
            $response->where('sales_person_id', $sales_person_id);
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

        $billed_amount = $response->sum('billed_amount');
        $damage_amount = $response->sum('damage_amount');
        $return_amount = $response->sum('return_amount');
        $adjusment_amount = $response->sum('adjusment_amount');
        $online_amount = $response->sum('online_amount');
        $cash_amount = $response->sum('cash_amount');
        $paid_amount = $online_amount + $cash_amount;
        $balance_amount = $response->sum('balance_amount');

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
        
        return view('admin.pages.credit-report.list', compact('metadata', 'billed_amount', 'online_amount', 'cash_amount', 'paid_amount', 'balance_amount', 'damage_amount', 'return_amount', 'adjusment_amount'));
    }
}
