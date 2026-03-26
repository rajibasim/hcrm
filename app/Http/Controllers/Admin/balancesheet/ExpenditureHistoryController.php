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

class ExpenditureHistoryController extends Controller{

    public function __construct(){
        $this->middleware('permission:expenditure_history_view|expenditure_history_create|expenditure_history_edit|expenditure_history_delete', ['only' => ['index','store']]);
        $this->middleware('permission:expenditure_history_create', ['only' => ['create','store']]);
        $this->middleware('permission:expenditure_history_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:expenditure_history_delete', ['only' => ['destroy']]);
        $this->title = 'Expenditure History';
        $this->slug = route('expenditure-history.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response =  BalanceSheetTransactions::where('deleted_at', '=', NULL)->orderBy('id', 'desc')->where('purpose', 2);

        // --- Filters --
        if ($request->filled('expenditure_purpose')) {
            $response->where('expenditure_purpose', $request->expenditure_purpose);
            $serach_data['expenditure_purpose'] = $request->expenditure_purpose;
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

        $responseSum =  BalanceSheetTransactions::where('deleted_at', '=', NULL)->orderBy('id', 'desc')->where('purpose', 2);
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $responseSum->whereBetween('entry_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $responseSum->whereDate('entry_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $responseSum->whereDate('entry_date', '<=', $request->end_date);
        }

        $sumObj = $responseSum->selectRaw("
            SUM(CASE WHEN expenditure_purpose = 1 THEN inventory_amount + online_amount + cash_amount ELSE 0 END) AS damage,
            SUM(CASE WHEN expenditure_purpose = 2 THEN inventory_amount + online_amount + cash_amount ELSE 0 END) AS daily_expenses,
            SUM(CASE WHEN expenditure_purpose = 3 THEN inventory_amount + online_amount + cash_amount ELSE 0 END) AS salary,
            SUM(CASE WHEN expenditure_purpose = 4 THEN inventory_amount + online_amount + cash_amount ELSE 0 END) AS rent,
            SUM(CASE WHEN expenditure_purpose = 5 THEN inventory_amount + online_amount + cash_amount ELSE 0 END) AS oil,
            SUM(CASE WHEN expenditure_purpose = 6 THEN inventory_amount + online_amount + cash_amount ELSE 0 END) AS other
        ")->first();
        
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
        
        return view('admin.pages.expenditure-history.list', compact('rows', 'metadata', 'sumObj'));
    }

    ### Create View
    public function create(Request $request){
        $metadata = array(
            'page_title' => $this->title . ' Add',
            'page_url' => $this->slug,
            'serach_data' => [],
            'breadcumb' => array(
                array(
                    'url' => '/dashboard',
                    'title' => 'Home',  
                ),
                array(
                    'url' => $this->slug,
                    'title' => $this->title,  
                ),
                array(
                    'url' => '',
                    'title' => 'Add',  
                )
            ),
        );
        $details = BalanceSheetTransactions::where('is_active', 1)->where('deleted_at', '=', NULL)->latest()->first();
        return view('admin.pages.expenditure-history.form', compact('metadata', 'details'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'entry_date' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{

            $opening_inventory_amount = $request->opening_inventory_amount;
            $opening_online_amount = $request->opening_online_amount;
            $opening_cash_amount = $request->opening_cash_amount;
            $inventory_amount = $request->inventory_amount;
            $online_amount = $request->online_amount;
            $cash_amount = $request->cash_amount;
            $entry_date = $request->entry_date;
            $closing_inventory_amount = $opening_inventory_amount - $inventory_amount;
            $closing_online_amount = $opening_online_amount - $online_amount;
            $closing_cash_amount = $opening_cash_amount - $cash_amount;
            
            $details = BalanceSheetTransactions::where('is_active', 1)->where('deleted_at', '=', NULL)->latest()->first();
            if($details){
                $details = $details->toArray();

                if($inventory_amount > $opening_inventory_amount){
                    $flash_data = array(
                        'status' => 'error',
                        'message' => 'Insufficient balance avalible.',
                    );

                    Session::put('flash_data', $flash_data); 
                    return redirect($this->slug);
                }

                if($online_amount > $opening_online_amount){
                    $flash_data = array(
                        'status' => 'error',
                        'message' => 'Insufficient balance avalible.',
                    );

                    Session::put('flash_data', $flash_data); 
                    return redirect($this->slug);
                }

                if($cash_amount > $opening_cash_amount){
                    $flash_data = array(
                        'status' => 'error',
                        'message' => 'Insufficient balance avalible.',
                    );

                    Session::put('flash_data', $flash_data); 
                    return redirect($this->slug);
                }


                $transData = array(
                    'opening_inventory_amount' =>  $opening_inventory_amount,
                    'opening_online_amount' =>  $opening_online_amount,
                    'opening_cash_amount' =>  $opening_cash_amount,
                    'inventory_amount' =>  $inventory_amount,
                    'online_amount' =>  $online_amount,
                    'cash_amount' =>  $cash_amount,
                    'entry_date' =>  $entry_date,
                    'closing_inventory_amount' =>  $closing_inventory_amount,
                    'closing_online_amount' =>  $closing_online_amount,
                    'closing_cash_amount' =>  $closing_cash_amount,
                    'purpose' => $request->purpose,
                    'created_by' => created_by(),
                    'updated_by' => updated_by(),
                    'expenditure_purpose' => $request->expenditure_purpose,
                    'note' => $request->note,
                );

                $created = BalanceSheetTransactions::query()->create($transData);
                if($created){
                    $flash_data = array(
                        'status' => 'success',
                        'message' => 'Successfully transfered.',
                    );
                }else{
                    $flash_data = array(
                        'status' => 'error',
                        'message' => 'Something went wrong, try again.',
                    );
                }
            }else{
                $flash_data = array(
                    'status' => 'error',
                    'message' => 'Something went wrong, try again.',
                );
            }

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }
}
