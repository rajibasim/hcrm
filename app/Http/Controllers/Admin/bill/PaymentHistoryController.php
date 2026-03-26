<?php

namespace App\Http\Controllers\Admin\bill;

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
use App\Models\User;
use Validator;

class PaymentHistoryController extends Controller{

    public function __construct(){
        $this->middleware('permission:bill_payment_history_view|bill_payment_history_create|bill_payment_history_edit|bill_payment_history_delete', ['only' => ['index','store']]);
        $this->middleware('permission:bill_payment_history_create', ['only' => ['create','store']]);
        $this->middleware('permission:bill_payment_history_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:bill_payment_history_delete', ['only' => ['destroy']]);
        $this->title = 'Bill Payment History';
        $this->slug = route('bil-payment-history.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $sales_person_id = intval(auth()->user()->sales_person_id);
        $response = PaymentHistory::withTrashed()->with('bill.SalesPerson')->orderBy('id', 'desc');

        if(isset($sales_person_id) && intval($sales_person_id) > 0){
            $response->whereHas('bill.SalesPerson', function ($q) use ($sales_person_id) {
                $q->where('sales_person_id', $sales_person_id);
            });
        }

        if ($request->wt_aprove) {
            $response = $response->where('bill_payment_histories.is_active', '0')->where('bill_payment_histories.deleted_at', '=', NULL);
        }
        
        // --- Filters ---
        if ($request->filled('customer_id')) {
            $response->whereHas('bill.customer', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_name);
            });

            $serach_data['customer_name'] = $request->customer_name;
        }

        // --- Filters ---
        if ($request->filled('bill_number')) {
            $response->where('bill_number', 'like', '%' . $request->bill_number . '%');
            $serach_data['bill_no'] = $request->bill_number;
        }

        if ($request->filled('sales_person_id')) {
            $response->whereHas('bill.SalesPerson', function ($q) use ($request) {
                $q->where('sales_person_id', $request->sales_person_id);
            });

            $serach_data['sales_person_id'] = $request->sales_person_id;
        }

        if ($request->filled('customer_id')) {
            $response->where('customer_id', $request->customer_id);
            $serach_data['customer_id'] = $request->customer_id;
        }

        if ($request->filled('invoice_date')) {
            $response->whereDate('invoice_date', $request->invoice_date);
            $serach_data['invoice_date'] = $request->invoice_date;
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
        return view('admin.pages.bill-payment-history.list', compact('rows', 'metadata', 'SalesPerson'));
    }

    ### Edit View
    public function edit($id){

        $paymentHistory = PaymentHistory::find($id);
        $billDetails = Bill::find($paymentHistory->bill_id);

        if($paymentHistory && $billDetails){
                $bill_online_amount = $billDetails->online_amount;
                $bill_cash_amount = $billDetails->cash_amount;
                $bill_balance_amount = $billDetails->balance_amount;

                $history_online_amount = $paymentHistory->online_amount;
                $history_cash_amount = $paymentHistory->cash_amount;
                $history_balance_amount = $paymentHistory->balance_amount;

                $bill_online_amount = $history_online_amount + $bill_online_amount;
                $bill_cash_amount = $history_cash_amount + $bill_cash_amount;
                $balance_amount = $bill_balance_amount - $history_balance_amount;

                $billData = array(
                    'balance_amount' => $balance_amount,
                    'online_amount' => $bill_online_amount,
                    'cash_amount' => $bill_cash_amount,
                    'updated_by' => updated_by(),
                );

                $updateBill = $billDetails->update($billData);
                //Update bill history
                $historyData = array(
                    'is_active' => 1,
                    'updated_by' => updated_by(),
                );

                $updateHistory = $paymentHistory->update($historyData);

                if($updateHistory =1 && $updateBill =1){
                    $details = BalanceSheetTransactions::where('is_active', 1)->where('deleted_at', '=', NULL)->latest()->first();
                    if($details){
                        $details = $details->toArray();
                        unset($details['is_active']);
                        unset($details['created_at']);
                        unset($details['updated_at']);
                        $details['bill_id'] = $paymentHistory->bill_id;
                        $details['purpose'] = 7;
                        $details['created_by'] = created_by();
                        $details['updated_by'] = updated_by();
                        $details['opening_cash_amount'] = $details['closing_cash_amount'];
                        $details['opening_online_amount'] = $details['closing_online_amount'];
                        $details['closing_cash_amount'] = $details['closing_cash_amount'] + $history_cash_amount;
                        $details['closing_online_amount'] = $details['closing_online_amount'] + $history_online_amount;
                        $details['cash_amount'] = $history_cash_amount;
                        $details['online_amount'] = $history_online_amount;
                        
                        $created = BalanceSheetTransactions::query()->create($details);
                    }

                    $flash_data = array(
                        'status' => 'success',
                        'message' => $this->title.' successfully payment accepted.',
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

    ### Delete Data
    public function destroy($id){
        $delete = PaymentHistory::find($id);
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

    ### View
    public function show($id){
        $metadata = array(
            'page_title' => $this->title . ' Details',
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
                    'title' => 'Details',  
                )
            ),
        );
        
        $details = BillEntry::find($id);
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->orderBy('area', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->where('area_id', '=', $details->area_id)->orderBy('id', 'asc')->get();
        $product = Product::with('unit')->with('category')->where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $productReturn = BillEntryProduct::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('bill_payment_history_id', '=', $id)->get();
        return view('admin.pages.bill-entry.show', compact('details', 'metadata', 'area', 'beat', 'customer', 'sales_person', 'productReturn'));
    }
}
