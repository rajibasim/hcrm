<?php

namespace App\Http\Controllers\Admin\customer;

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
use App\Models\Bill;
use Validator;

class CustomerController extends Controller{

    public function __construct(){
        $this->middleware('permission:customer_view|customer_create|customer_edit|customer_delete', ['only' => ['index','store']]);
        $this->middleware('permission:customer_create', ['only' => ['create','store']]);
        $this->middleware('permission:customer_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:customer_delete', ['only' => ['destroy']]);
        $this->title = 'Customer';
        $this->slug = route('customer.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = Customer::where('deleted_at', '=', NULL);
        if($request->party_name){
            $response = $response->where('party_name', 'like', '%' . $request->party_name . '%');
            $serach_data['party_name'] = $request->party_name;
        }

        if($request->party_code){
            $response = $response->where('party_code', 'like', '%' . $request->party_code . '%');
            $serach_data['party_code'] = $request->party_code;
        }

        if($request->beat){
            $response = $response->where('beat', 'like', '%' . $request->beat . '%');
            $serach_data['beat'] = $request->beat;
        }

        if($request->phone_no){
            $response = $response->where('phone_no', '=', $request->phone_no);
            $serach_data['phone_no'] = $request->phone_no;
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
        return view('admin.pages.customer.list', compact('rows', 'metadata'));
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

        return view('admin.pages.customer.form', compact('metadata'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'party_name' => ['required', 'unique:customers,party_name,NULL,id,phone_no,'.$request->input('phone_no')]
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['created_by'] = created_by();
            $created = Customer::query()->create($data);
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
        
        $details = Customer::find($id);
        return view('admin.pages.customer.form', compact('details', 'metadata'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'party_name' => ['required', 'unique:customers,party_name,'.$id.',id,phone_no,'.$request->input('phone_no')]
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['updated_by'] = updated_by();
            $update = Customer::find($id);
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
        
            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    ### Delete Data
    public function destroy($id){
        $delete = Customer::find($id);
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
            'page_title' => $this->title . ' View',
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
                    'title' => 'View',  
                )
            ),
        );
        
        $details = Customer::find($id);
        $rows = Bill::where('deleted_at', '=', NULL)->where('customer_id', $id)->with('customer')->with('DeliveryStatus')->with('SalesPerson')->orderBy('id', 'desc')->get();
        return view('admin.pages.customer.show', compact('details', 'metadata', 'rows'));
    }
}
