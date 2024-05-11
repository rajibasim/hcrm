<?php

namespace App\Http\Controllers\admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Service;
use Validator;

class ServiceController extends Controller{

    public function __construct(){
        $this->middleware('permission:service-list|service-create|service-edit|service-delete', ['only' => ['index','store']]);
        $this->middleware('permission:service-create', ['only' => ['create','store']]);
        $this->middleware('permission:service-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:service-delete', ['only' => ['destroy']]);
        $this->title = 'Service';
        $this->slug = route('service.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = DB::table('services')->where('deleted_at', '=', NULL);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
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
        
        return view('admin.pages.service.list', compact('rows', 'metadata'));
    }

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
        return view('admin.pages.service.form', compact('metadata'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:services,name',
            'price' => 'required',
            'duration' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            Service::query()->create($data);
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully created.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

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
       
        $details = Service::find($id);
        return view('admin.pages.service.form', compact('details', 'metadata'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:services,name,' . $id,
            'price' => 'required',
            'duration' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            unset($data['id']);
            $update = Service::find($id);
            $update->update($data);
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully updated.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    public function destroy($id){
        $delete = Service::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
