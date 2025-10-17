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
use App\Models\ReturnEntry;
use App\Models\ReturnEntryProduct;
use Validator;

class ReturnEntryController extends Controller{

    public function __construct(){
        $this->middleware('permission:return_entry_view|return_entry_create|return_entry_edit|return_entry_delete', ['only' => ['index','store']]);
        $this->middleware('permission:return_entry_create', ['only' => ['create','store']]);
        $this->middleware('permission:return_entry_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:return_entry_delete', ['only' => ['destroy']]);
        $this->title = 'Return Entry';
        $this->slug = route('return-entry.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = ReturnEntry::with('area')->with('beat')->with('customer')->with('sales_person')->where('deleted_at', '=', NULL);
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
        return view('admin.pages.return-entry.list', compact('rows', 'metadata', 'area', 'beat', 'customer', 'sales_person'));
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
        $product = Product::with('unit')->with('category')->where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.return-entry.form', compact('metadata', 'area', 'beat', 'customer', 'product', 'sales_person'));
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
                'note' => $request->note, 
                'total_amount' => $request->total_amount, 
                'created_by' => created_by(),
            );
            $created = ReturnEntry::query()->create($return_data);
            if($created){
                $return_entry_id = $created->id;
                foreach ($request->product_id as $key => $value) {
                    $product_data = array(
                        'return_entry_id' => $return_entry_id,
                        'return_date' => $request->return_date,
                        'product_id' => $value,
                        'product_qty' => $request->product_qty_hidden[$key],
                        'product_unit_price' => $request->product_unit_price_hidden[$key],
                        'sub_total' => $request->sub_total_hidden[$key],
                        'created_by' => created_by(),
                        'updated_by' => updated_by(),
                    );

                    $product = ReturnEntryProduct::query()->create($product_data);
                }
            }

            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully created.',
            );

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
        
        $details = ReturnEntry::find($id);
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->orderBy('area', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->where('area_id', '=', $details->area_id)->orderBy('id', 'asc')->get();
        $product = Product::with('unit')->with('category')->where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $productReturn = ReturnEntryProduct::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('return_entry_id', '=', $id)->get();
        return view('admin.pages.return-entry.form', compact('details', 'metadata', 'area', 'beat', 'customer', 'product', 'sales_person', 'productReturn'));
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
                'updated_by' => updated_by(),
            );
            $update = ReturnEntry::find($id);
            $update = $update->update($return_data);
            if($update){
                $return_entry_id = $id;
                $productDelete = DB::table('return_entry_products')->where('return_entry_id', $return_entry_id)->delete();
                foreach ($request->product_id as $key => $value) {
                    $product_data = array(
                        'return_entry_id' => $return_entry_id,
                        'return_date' => $request->return_date,
                        'product_id' => $value,
                        'product_qty' => $request->product_qty_hidden[$key],
                        'product_unit_price' => $request->product_unit_price_hidden[$key],
                        'sub_total' => $request->sub_total_hidden[$key],
                        'created_by' => created_by(),
                        'updated_by' => updated_by(),
                    );

                    $product = ReturnEntryProduct::query()->create($product_data);
                }

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
        $delete = ReturnEntry::find($id);
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
        
        $details = ReturnEntry::find($id);
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->orderBy('area', 'asc')->get();
        $customer = Customer::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->where('area_id', '=', $details->area_id)->orderBy('id', 'asc')->get();
        $product = Product::with('unit')->with('category')->where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $sales_person = SalesPerson::where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        $productReturn = ReturnEntryProduct::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('return_entry_id', '=', $id)->get();
        return view('admin.pages.return-entry.show', compact('details', 'metadata', 'area', 'beat', 'customer', 'product', 'sales_person', 'productReturn'));
    }
}
