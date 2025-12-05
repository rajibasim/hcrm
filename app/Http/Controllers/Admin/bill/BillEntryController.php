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
use Validator;

class BillEntryController extends Controller{

    public function __construct(){
        $this->middleware('permission:bill_entry_view|bill_entry_create|bill_entry_edit|bill_entry_delete', ['only' => ['index','store']]);
        $this->middleware('permission:bill_entry_create', ['only' => ['create','store']]);
        $this->middleware('permission:bill_entry_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:bill_entry_delete', ['only' => ['destroy']]);
        $this->title = 'Bill Entry';
        $this->slug = route('bill.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = Bill::where('deleted_at', '=', NULL)->with('customer')->with('DeliveryStatus')->with('SalesPerson');
        
        // --- Filters ---
        if ($request->filled('bill_number')) {
            $response->where('bill_number', 'like', '%' . $request->bill_number . '%');
            $serach_data['bill_no'] = $request->bill_no;
        }

        if ($request->filled('sales_person_id')) {
            $response->where('sales_person_id', $request->sales_person_id);
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
        //$area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('area', 'asc')->get();
        //$beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('id', 'asc')->get();
        $SalesPerson = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.bill.list', compact('rows', 'metadata', 'customer', 'SalesPerson'));
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

        $DeliveryStatus = DeliveryStatus::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('name', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('id', 'asc')->get();
        $SalesPerson = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.bill.form', compact('metadata', 'customer', 'DeliveryStatus', 'SalesPerson'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'bill_number' => 'required|string|max:255|unique:bills,bill_number',
            'invoice_date' => 'required',
            'delivery_status_update_date' => 'required',
            'delivery_status_id' => 'required',
            'sales_person_id' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'billed_amount' => 'required|numeric|min:0',
            'return_amount' => 'required|numeric|min:0',
            'damage_amount' => 'required|numeric|min:0',
            'adjusment_percent' => 'required|numeric|min:0|max:100',
            'adjusment_amount' => 'required|numeric|min:0',
            'balance_amount' => 'required|numeric|min:0',
            //'is_active' => 'required|boolean',
            //'note' => 'required|string|max:500',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = array(
                'financial_year' => config('config.financial_year'),
                'bill_number' => $request->bill_number, 
                'invoice_date' => $request->invoice_date, 
                'delivery_status_update_date' => $request->delivery_status_update_date, 
                'delivery_status_id' => $request->delivery_status_id, 
                'sales_person_id' => $request->sales_person_id, 
                'customer_id' => $request->customer_id, 
                'billed_amount' => $request->billed_amount ?? 0,
                'return_amount' => $request->return_amount ?? 0,
                'damage_amount' => $request->damage_amount ?? 0,
                'adjusment_percent' => $request->adjusment_percent ?? 0,
                'adjusment_amount' => $request->adjusment_amount ?? 0,
                'balance_amount' => $request->balance_amount ?? 0,
                'note' => $request->note, 
                'is_active' => $request->is_active,
                'created_by' => created_by(),
            );
            $created = Bill::query()->create($data);

            $online_amount = 0;
            $cash_amount = 0;
            if($created){
                $bill_id = $created->id;
                //insert history table
                if ($request->payment_date && count($request->payment_date) > 0) {
                    foreach ($request->payment_date as $index => $date) {
                        if($date){
                            
                            $total = array_sum([
                                $request->online_amount[$index] ?? 0,
                                $request->cash_amount[$index] ?? 0,
                                //$request->damage_amount ?? 0,
                                //$request->adjusment_amount ?? 0,
                                //$request->return_amount ?? 0,
                            ]);

                            $payment = new PaymentHistory();
                            $payment->bill_id = $bill_id; 
                            $payment->payment_date = $date;
                            $payment->online_amount = $request->online_amount[$index] ?? 0;
                            $payment->cash_amount = $request->cash_amount[$index] ?? 0;
                            $payment->balance_amount = $total; // Total payment receved on this day
                            $payment->is_active = 0;
                            $payment->created_by = created_by();
                            $payment->updated_by = updated_by();
                            
                            //$online_amount = $online_amount + $request->online_amount[$index] ?? 0;
                            //$cash_amount = $cash_amount + $request->cash_amount[$index] ?? 0;

                            // Handle file upload if any
                            $payment->attachment = '';
                            if ($request->hasFile('attachment') && isset($request->file('attachment')[$index])) {
                                $file = $request->file('attachment')[$index];
                                $filename = time() . '_' . $file->getClientOriginalName();
                                $path = public_path('uploads/attachment/'.$request->bill_number);
                                $file->move($path, $filename);
                                $payment->attachment = $filename;
                            }

                            $payment->save();
                        }
                    }
                }

                //insert stats table
                $status_data = array(
                    'bill_id' => $bill_id,
                    'delivery_status_id' => $request->delivery_status_id, 
                    'updated_by' => updated_by(),
                    'created_by' => created_by(),
                );

                $created = StatusHistory::query()->create($status_data);

                //update bill table
                /*$billdata['updated_by'] = updated_by();
                $billdata['online_amount'] = $online_amount;
                $billdata['cash_amount'] = $cash_amount;
                $update = Bill::find($bill_id);
                $update = $update->update($billdata);*/

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
        
        $details = Bill::find($id);
        $DeliveryStatus = DeliveryStatus::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('name', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('id', 'asc')->get();
        $SalesPerson = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $paymentHistory = PaymentHistory::where('deleted_at', '=', NULL)->where('bill_id', '=', $id)->get();
        return view('admin.pages.bill.form', compact('details', 'metadata', 'customer', 'DeliveryStatus', 'SalesPerson', 'paymentHistory'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'bill_number' => 'required|string|max:50|unique:bills,bill_number,' . $id,
            'invoice_date' => 'required',
            'delivery_status_update_date' => 'required',
            'delivery_status_id' => 'required',
            'sales_person_id' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'billed_amount' => 'required|numeric|min:0',
            'return_amount' => 'required|numeric|min:0',
            'damage_amount' => 'required|numeric|min:0',
            'adjusment_percent' => 'required|numeric|min:0|max:100',
            'adjusment_amount' => 'required|numeric|min:0',
            'balance_amount' => 'required|numeric|min:0',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = array(
                'bill_number' => $request->bill_number, 
                'invoice_date' => $request->invoice_date, 
                'delivery_status_update_date' => $request->delivery_status_update_date, 
                'delivery_status_id' => $request->delivery_status_id, 
                'sales_person_id' => $request->sales_person_id, 
                'customer_id' => $request->customer_id, 
                'billed_amount' => $request->billed_amount ?? 0,
                'return_amount' => $request->return_amount ?? 0,
                'damage_amount' => $request->damage_amount ?? 0,
                'adjusment_percent' => $request->adjusment_percent ?? 0,
                'adjusment_amount' => $request->adjusment_amount ?? 0,
                'balance_amount' => $request->balance_amount ?? 0,
                'note' => $request->note, 
                'is_active' => $request->is_active,
                'updated_by' => updated_by(),
            );
            $update = Bill::find($id);
            $updated = $update->update($data);
            $online_amount = 0;
            $cash_amount = 0;
            if($update){
                $bill_id = $id;

                //remove from payment history
                $old_ids = $request->old_ids;
                if($old_ids){
                    $old_ids_arr = explode(',', $old_ids);
                    foreach ($old_ids_arr as $olkey => $orvalue) {
                        //delete old data
                        $delete = PaymentHistory::withTrashed()->find($orvalue);
                        $delete = $delete->forceDelete();
                    }
                }


                //insert history table
                if ($request->payment_date && count($request->payment_date) > 0) {
                    foreach ($request->payment_date as $index => $date) {
                        if($date){
                            $total = array_sum([
                                $request->online_amount[$index] ?? 0,
                                $request->cash_amount[$index] ?? 0,
                                //$request->damage_amount ?? 0,
                                //$request->adjusment_amount ?? 0,
                                //$request->return_amount ?? 0,
                            ]);

                            $payment = new PaymentHistory();
                            $payment->bill_id = $bill_id; 
                            $payment->payment_date = $date;
                            $payment->online_amount = $request->online_amount[$index] ?? 0;
                            $payment->cash_amount = $request->cash_amount[$index] ?? 0;
                            $payment->balance_amount = $total; // Total payment receved on this day
                            $payment->created_by = created_by();
                            $payment->updated_by = updated_by();
                            
                            //$online_amount = $online_amount + $request->online_amount[$index] ?? 0;
                            //$cash_amount = $cash_amount + $request->cash_amount[$index] ?? 0;

                            // Handle file upload if any
                            $payment->attachment = '';
                            if ($request->hasFile('attachment') && isset($request->file('attachment')[$index])) {
                                $file = $request->file('attachment')[$index];
                                $filename = time() . '_' . $file->getClientOriginalName();
                                $path = public_path('uploads/attachment/'.$request->bill_number);
                                $file->move($path, $filename);
                                $payment->attachment = $filename;
                            }

                            $payment->save();
                        }
                    }
                }

                //insert stats table
                $status_data = array(
                    'bill_id' => $bill_id,
                    'delivery_status_id' => $request->delivery_status_id, 
                    'updated_by' => updated_by(),
                    'created_by' => created_by(),
                );

                $created = StatusHistory::query()->create($status_data);

                //update bill table
                /*$billdata['updated_by'] = updated_by();
                $billdata['online_amount'] = $online_amount;
                $billdata['cash_amount'] = $cash_amount;
                $updated = $update->update($billdata);*/

                if($created){
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

    ### Delete Data
    public function destroy($id){
        $delete = Bill::withTrashed()->find($id);
        $delete = $delete->forceDelete();
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
        $productReturn = BillEntryProduct::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('bill_entry_id', '=', $id)->get();
        return view('admin.pages.bill-entry.show', compact('details', 'metadata', 'area', 'beat', 'customer', 'sales_person', 'productReturn'));
    }
}
