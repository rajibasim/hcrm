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

class InventoryHistoryController extends Controller{

    public function __construct(){
        $this->middleware('permission:inventory_purchase_view|inventory_purchase_create|inventory_purchase_edit|inventory_purchase_delete', ['only' => ['index','store']]);
        $this->middleware('permission:inventory_purchase_create', ['only' => ['create','store']]);
        $this->middleware('permission:inventory_purchase_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:inventory_purchase_delete', ['only' => ['destroy']]);
        $this->title = 'Inventory History';
        $this->slug = route('inventory-history.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response =  BalanceSheetTransactions::where('deleted_at', '=', NULL)->orderBy('id', 'desc')->where('purpose', 1);

        // --- Filters --
        if ($request->filled('invoice_number')) {
            $response->where('invoice_number', 'LIKE', '%' . $request->invoice_number . '%');
            $serach_data['invoice_number'] = $request->invoice_number;
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

        /* Purpose = 1 (Invest) */
        $invest_inventory_amount = (clone $response)->where('purpose', 1)->sum('inventory_amount');
        $invest_online_amount    = (clone $response)->where('purpose', 1)->sum('online_amount');
        $invest_cash_amount      = (clone $response)->where('purpose', 1)->sum('cash_amount');

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
        
        return view('admin.pages.inventory-history.list', compact('rows', 'metadata', 'invest_inventory_amount', 'invest_online_amount', 'invest_cash_amount'));
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
        return view('admin.pages.inventory-history.form', compact('metadata'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'entry_date' => 'required',
            'invoice_number' => 'required',
            'inventory_amount' => 'required|numeric|min:0',
            'online_amount' => 'required|numeric|min:0',
            'claim_amount' => 'required|numeric|min:0',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $purpose = 1;
            $inventory_amount = $request->inventory_amount;
            $online_amount = $request->online_amount;
            $claim_amount = $request->claim_amount;
            $total_inventory_amount = $online_amount + $claim_amount;
            if($inventory_amount != $total_inventory_amount){
                $flash_data = array(
                    'status'  => 'error',
                    'message' => 'Inventory amount will be same as total amount online or claim .',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }

            $data = $request->all();
            $data['financial_year'] = config('config.financial_year');
            $data['created_by'] = created_by();
            $opening_inventory_amount = 0;
            $opening_online_amount = 0;
            $opening_cash_amount = 0;
            //Only set previous data
            $profit_amount = 0;
            $opening_profit_amount = 0;
            $closing_profit_amount = 0;
            $details = BalanceSheetTransactions::where('is_active', 1)->where('deleted_at', '=', NULL)->latest()->first();
            if($details){
                if($details->closing_online_amount < $online_amount){
                    $flash_data = array(
                        'status'  => 'error',
                        'message' => 'Insufficient funds avalible in online.',
                    );

                    Session::put('flash_data', $flash_data); 
                    return redirect($this->slug);
                }
            }else{
                $flash_data = array(
                    'status'  => 'error',
                    'message' => 'Insufficient funds avalible in online.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }
            if($details){
                $opening_inventory_amount = $details->closing_inventory_amount ?? 0;
                $opening_online_amount = $details->closing_online_amount ?? 0;
                $opening_cash_amount = $details->opening_cash_amount ?? 0;
                $closing_cash_amount = $details->closing_cash_amount ?? 0;
            }

            if($purpose == 1){
                $closing_inventory_amount = $opening_inventory_amount + $inventory_amount;
                $closing_online_amount = $opening_online_amount - $online_amount;
            }

            //$total_amount = $closing_inventory_amount + $closing_online_amount + $closing_cash_amount;

            $data['purpose'] = 1;
            $data['opening_inventory_amount'] = $opening_inventory_amount;
            $data['opening_online_amount'] = $opening_online_amount;
            $data['opening_cash_amount'] = $opening_cash_amount;
            $data['closing_inventory_amount'] = $closing_inventory_amount;
            $data['closing_online_amount'] = $closing_online_amount;
            $data['closing_cash_amount'] = $closing_cash_amount;
            $data['claim_amount'] = $claim_amount;
            
            $created = BalanceSheetTransactions::query()->create($data);
            if($created){
                $flash_data = array(
                    'status' => 'success',
                    'message' => $this->title.' successfully created.',
                );
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

    ### Edit View
    public function edit($id){
        $metadata = array(
            'page_title' => $this->title . ' Edit',
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
                    'title' => 'Edit',  
                )
            ),
        );
        
        $details = BalanceSheetTransactions::find($id);
        return view('admin.pages.inventory-history.form', compact('details', 'metadata'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'entry_date' => 'required',
            'purpose' => 'required',
            'inventory_amount' => 'required|numeric|min:0',
            'online_amount' => 'required|numeric|min:0',
            'cash_amount' => 'required|numeric|min:0',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{

            $inventory_amount = $request->inventory_amount;
            $online_amount = $request->online_amount;
            $cash_amount = $request->cash_amount;
            $purpose = $request->purpose;

            $data = $request->all();
            $data['updated_by'] = updated_by();
            $update = BalanceSheetTransactions::find($id);

            $opening_inventory_amount = 0;
            $opening_online_amount = 0;
            $opening_cash_amount = 0;
            $total_amount = 0;
            $details = BalanceSheetTransactions::where('is_active', 1)->whereNull('deleted_at')->latest()->skip(1)->first();

            if($details){
                $opening_inventory_amount = $details->closing_inventory_amount ?? 0;
                $opening_online_amount = $details->closing_online_amount ?? 0;
                $opening_cash_amount = $details->closing_cash_amount ?? 0;
            }

            if($purpose == 1){
                $closing_inventory_amount = $opening_inventory_amount + $inventory_amount;
                $closing_online_amount = $opening_online_amount + $online_amount;
                $closing_cash_amount = $opening_cash_amount + $cash_amount;
            }

            if($purpose == 2){
                $closing_inventory_amount = $opening_inventory_amount - $inventory_amount;
                $closing_online_amount = $opening_online_amount - $online_amount;
                $closing_cash_amount = $opening_cash_amount - $cash_amount;
            }

            $flash_data = [];
            switch (true) {
                case ($total_amount < 0):
                    $flash_data = array(
                        'status'  => 'error',
                        'message' => 'Total amount cannot be negative.',
                    );
                    break;
                case ($closing_inventory_amount < 0):
                    $flash_data = array(
                        'status'  => 'error',
                        'message' => 'Inventory amount cannot be negative.',
                    );
                    break;
                case ($closing_cash_amount < 0):
                    $flash_data = array(
                        'status'  => 'error',
                        'message' => 'Cash amount cannot be negative.',
                    );
                    break;
                case ($closing_online_amount < 0):
                    $flash_data = array(
                        'status'  => 'error',
                        'message' => 'Online amount cannot be negative.',
                    );
                    break;
            }

            if(empty($flash_data)){
                $data['opening_inventory_amount'] = $opening_inventory_amount;
                $data['opening_online_amount'] = $opening_online_amount;
                $data['opening_cash_amount'] = $opening_cash_amount;
                $data['closing_inventory_amount'] = $closing_inventory_amount;
                $data['closing_online_amount'] = $closing_online_amount;
                $data['closing_cash_amount'] = $closing_cash_amount;
                $data['total_amount'] = $total_amount;

                $update = $update->update($data);
                if($update){
                    $flash_data = array(
                        'status' => 'success',
                        'message' => $this->title.' successfully updated.',
                    );
                }else{
                    $flash_data = array(
                        'status' => 'error',
                        'message' => 'Something went wrong, try again.',
                    );
                }
            }

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    ### Delete Data
    public function destroy($id){
        $delete = BalanceSheetTransactions::find($id);
        $delete = $delete->delete();

        if($delete){
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully deleted.',
            );
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
