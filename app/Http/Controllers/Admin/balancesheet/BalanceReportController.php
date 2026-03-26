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
use Validator;

class BalanceReportController extends Controller{

    public function __construct(){
        $this->middleware('permission:balance_report_view|balance_report_create|balance_report_edit|balance_report_delete', ['only' => ['index','store']]);
        $this->middleware('permission:balance_report_create', ['only' => ['create','store']]);
        $this->middleware('permission:balance_report_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:balance_report_delete', ['only' => ['destroy']]);
        $this->title = 'Balance Report';
        $this->slug = route('balance-report.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response =  BalanceSheets::where('deleted_at', '=', NULL);

        // --- Filters --
        if ($request->filled('purpose')) {
            $response->where('purpose', $request->purpose);
            $serach_data['purpose'] = $request->purpose;
        }

        if ($request->filled('type')) {
            $response->where('type', $request->type);
            $serach_data['type'] = $request->type;
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
        
        return view('admin.pages.balance-report.list', compact('rows', 'metadata'));
    }
}
