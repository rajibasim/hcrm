<?php

namespace App\Http\Controllers\Admin\return_entry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Area;
use App\Models\Beat;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesPerson;
use App\Models\BillEntry;
use App\Models\BillEntryProduct;
use Validator;

class BillEntryController extends Controller{

    public function __construct(){
        $this->middleware('permission:bill_entry_view|bill_entry_create|bill_entry_edit|bill_entry_delete', ['only' => ['index','store']]);
        $this->middleware('permission:bill_entry_create', ['only' => ['create','store']]);
        $this->middleware('permission:bill_entry_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:bill_entry_delete', ['only' => ['destroy']]);
        $this->title = 'Bill Entry';
        $this->slug = route('bill-entry.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = BillEntry::with('area')->with('beat')->with('customer')->with('sales_person')->where('deleted_at', '=', NULL);
        if($request->bill_no){
            $response = $response->where('bill_no', 'like', '%' . $request->bill_no . '%');
            $serach_data['bill_no'] = $request->bill_no;
        }

        if($request->sales_person_id){
            $response = $response->where('sales_person_id', '=', $request->sales_person_id);
            $serach_data['sales_person_id'] = $request->sales_person_id;
        }

        if($request->beat_id){
            $response = $response->where('beat_id', '=', $request->beat_id);
            $serach_data['beat_id'] = $request->beat_id;
        }

        if($request->area_id){
            $response = $response->where('area_id', '=', $request->area_id);
            $serach_data['area_id'] = $request->area_id;
        }

        if($request->customer_id){
            $response = $response->where('customer_id', '=', $request->customer_id);
            $serach_data['customer_id'] = $request->customer_id;
        }

        if($request->start_date){
            $response = $response->whereDate('return_date', '>=', $request->start_date);
            $serach_data['start_date'] = $request->start_date;
        }

        if($request->end_date){
            $response = $response->whereDate('return_date', '<=', $request->end_date);
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
        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('area', 'asc')->get();
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('id', 'asc')->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.bill-entry.list', compact('rows', 'metadata', 'area', 'beat', 'customer', 'sales_person'));
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

        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('area', 'asc')->get();
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('id', 'asc')->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.bill-entry.form', compact('metadata', 'area', 'beat', 'customer', 'sales_person'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'return_date' => ['required']
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $return_data = array(
                'bill_no' => $request->bill_no, 
                'return_date' => $request->return_date, 
                'sales_person_id' => $request->sales_person_id, 
                'beat_id' => $request->beat_id, 
                'area_id' => $request->area_id, 
                'customer_id' => $request->customer_id, 
                'total_amount' => $request->total_amount,
                'online_amount' => $request->online_amount,
                'offline_amount' => $request->offline_amount,
                'balance_amount' => $request->balance_amount,
                'note' => $request->note, 
                'created_by' => created_by(),
            );
            $created = BillEntry::query()->create($return_data);

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
        
        $details = BillEntry::find($id);
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->orderBy('area', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->where('area_id', '=', $details->area_id)->orderBy('id', 'asc')->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.bill-entry.form', compact('details', 'metadata', 'area', 'beat', 'customer', 'sales_person'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'return_date' => ['required']
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $return_data = array(
                'bill_no' => $request->bill_no, 
                'return_date' => $request->return_date, 
                'sales_person_id' => $request->sales_person_id, 
                'beat_id' => $request->beat_id, 
                'area_id' => $request->area_id, 
                'customer_id' => $request->customer_id, 
                'note' => $request->note, 
                'total_amount' => $request->total_amount,
                'online_amount' => $request->online_amount,
                'offline_amount' => $request->offline_amount,
                'balance_amount' => $request->balance_amount,
                'updated_by' => updated_by(),
            );
            $update = BillEntry::find($id);
            $update = $update->update($return_data);
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
        
            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    ### Delete Data
    public function destroy($id){
        $delete = BillEntry::find($id);
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
        $productReturn = BillEntryProduct::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('bill_entry_id', '=', $id)->get();
        return view('admin.pages.bill-entry.show', compact('details', 'metadata', 'area', 'beat', 'customer', 'sales_person', 'productReturn'));
    }
}
