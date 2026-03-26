<?php

namespace App\Http\Controllers\Admin\balancesheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BalanceSheetTransactions;
use Validator;

class PaymentHistoryController extends Controller{

    public function __construct(){
        $this->middleware('permission:payment_history_view|payment_history_create|payment_history_edit|payment_history_delete', ['only' => ['index','store']]);
        $this->middleware('permission:payment_history_create', ['only' => ['create','store']]);
        $this->middleware('permission:payment_history_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:payment_history_delete', ['only' => ['destroy']]);
        $this->title = 'Payment History';
        $this->slug = route('inventory-billed.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response =  BalanceSheetTransactions::where('deleted_at', '=', NULL)->orderBy('id', 'desc')->where('purpose', 7);

        // --- Filters --
        if ($request->filled('bill_number')) {
            $response->whereHas('billData', function ($q) use ($request) {
                $q->where('bill_number', 'LIKE', '%' . $request->bill_number . '%');
            });
            $serach_data['bill_number'] = $request->bill_number;
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $response->whereBetween('entry_date', [$request->start_date, $request->end_date]);
            $serach_data['start_date'] = $request->start_date;
            $serach_data['end_date'] = $request->end_date;
        } elseif ($request->filled('start_date')) {
            $response->whereDate('entry_date', '>=', $request->start_date);
            $serach_data['start_date'] = $request->start_date;
        } elseif ($request->filled('end_date')) {
            $response->whereDate('entry_date', '<=', $request->end_date);
            $serach_data['end_date'] = $request->end_date;
        }

        /* Pagination */
        $rows = (clone $response)->paginate(10);

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
        
        return view('admin.pages.payment-history.list', compact('rows', 'metadata'));
    }
}
