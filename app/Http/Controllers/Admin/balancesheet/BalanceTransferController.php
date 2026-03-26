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

class BalanceTransferController extends Controller{

    public function __construct(){
        $this->middleware('permission:balance_transfer_view|balance_transfer_create|balance_transfer_edit|balance_transfer_delete', ['only' => ['index','store']]);
        $this->middleware('permission:balance_transfer_create', ['only' => ['create','store']]);
        $this->middleware('permission:balance_transfer_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:balance_transfer_delete', ['only' => ['destroy']]);
        $this->title = 'Balance Transfer';
        $this->slug = route('balance-transfer.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response =  BalanceSheetTransactions::where('deleted_at', '=', NULL)->orderBy('id', 'desc')->where('purpose', 3);

        // --- Filters --
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
        
        return view('admin.pages.balance-transfer.list', compact('rows', 'metadata'));
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
        return view('admin.pages.balance-transfer.form', compact('metadata', 'details'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'entry_date' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{

            $amount = $request->amount;
            $details = BalanceSheetTransactions::where('is_active', 1)->where('deleted_at', '=', NULL)->latest()->first();
            if($details && $amount > 0){
                $details = $details->toArray();

                if($request->type == 3){
                    if($amount > $details['closing_cash_amount']){
                        $flash_data = array(
                            'status' => 'error',
                            'message' => 'Insufficient balance avalible for transfer.',
                        );

                        Session::put('flash_data', $flash_data); 
                        return redirect($this->slug);
                    }
                }else if($request->type == 4){
                    if($amount > $details['closing_online_amount']){
                        $flash_data = array(
                            'status' => 'error',
                            'message' => 'Insufficient balance avalible for transfer.',
                        );

                        Session::put('flash_data', $flash_data); 
                        return redirect($this->slug);
                    }
                }

                unset($details['is_active']);
                unset($details['created_at']);
                unset($details['updated_at']);
                unset($details['bill_id']);
                unset($details['invoice_number']);
                unset($details['billed_amount']);
                unset($details['profit_mergine']);
                unset($details['profit_amount']);
                unset($details['inventory_amount']);

                $details['entry_date'] = $request->entry_date;
                $details['cash_amount'] = $amount;
                $details['online_amount'] = $amount;
                $details['purpose'] = $request->purpose;
                $details['created_by'] = created_by();
                $details['updated_by'] = updated_by();
                $details['opening_cash_amount'] = $details['closing_cash_amount'];
                $details['opening_online_amount'] = $details['closing_online_amount'];
                if($request->type == 3){
                    $details['closing_cash_amount'] = $details['closing_cash_amount'] - $amount;
                    $details['closing_online_amount'] = $details['closing_online_amount'] + $amount;
                }else if($request->type == 4){
                    $details['closing_cash_amount'] = $details['closing_cash_amount'] + $amount;
                    $details['closing_online_amount'] = $details['closing_online_amount'] - $amount;
                }
                $details['type'] = $request->type;
                $details['note'] = $request->note;
                $created = BalanceSheetTransactions::query()->create($details);
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
