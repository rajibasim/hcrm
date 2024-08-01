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
        $response = Customer::with('area')->with('beat')->where('deleted_at', '=', NULL);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
        }

        if($request->unit_id){
            $response = $response->where('unit_id', '=', $request->unit_id);
            $serach_data['unit_id'] = $request->unit_id;
        }

        if($request->category_id){
            $response = $response->where('category_id', '=', $request->category_id);
            $serach_data['category_id'] = $request->category_id;
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
        return view('admin.pages.customer.list', compact('rows', 'metadata', 'area', 'beat'));
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
        return view('admin.pages.customer.form', compact('metadata', 'area', 'beat'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'store_name' => ['required', 'unique:customers,store_name,NULL,id,mobile,'.$request->input('mobile')]
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
        $area = Area::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->where('beat_id', '=', $details->beat_id)->orderBy('area', 'asc')->get();
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        return view('admin.pages.customer.form', compact('details', 'metadata', 'area', 'beat'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'store_name' => ['required', 'unique:customers,store_name,'.$id.',id,mobile,'.$request->input('mobile')]
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
}
