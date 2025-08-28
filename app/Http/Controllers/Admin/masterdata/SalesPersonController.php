<?php

namespace App\Http\Controllers\Admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Category;
use App\Models\Unit;
use App\Models\SalesPerson;
use Validator;

class SalesPersonController extends Controller{

    public function __construct(){
        $this->middleware('permission:sales_person_view|sales_person_create|sales_person_edit|sales_person_delete', ['only' => ['index','store']]);
        $this->middleware('permission:sales_person_create', ['only' => ['create','store']]);
        $this->middleware('permission:sales_person_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:sales_person_delete', ['only' => ['destroy']]);
        $this->title = 'Sales Person';
        $this->slug = route('sales-person.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = SalesPerson::where('deleted_at', '=', NULL);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
        }

        if($request->email){
            $response = $response->where('email', 'like', '%' . $request->email . '%');
            $serach_data['email'] = $request->email;
        }

        if($request->mobile){
            $response = $response->where('mobile', 'like', '%' . $request->mobile . '%');
            $serach_data['mobile'] = $request->mobile;
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
       
        return view('admin.pages.sales-person.list', compact('rows', 'metadata'));
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
        return view('admin.pages.sales-person.form', compact('metadata'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|unique:sales_person,email',
            'mobile' => 'required|unique:sales_person,mobile',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['created_by'] = created_by();
            $created = SalesPerson::query()->create($data);
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
        
        $details = SalesPerson::find($id);
        return view('admin.pages.sales-person.form', compact('details', 'metadata'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|unique:sales_person,email,' . $id,
            'mobile' => 'required|unique:sales_person,mobile,' . $id,
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['updated_by'] = updated_by();
            $update = Sales_person::find($id);
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
        $delete = SalesPerson::find($id);
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
