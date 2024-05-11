<?php

namespace App\Http\Controllers\admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Activity;
use Validator;

class ActivityController extends Controller{

    public function __construct(){
        $this->middleware('permission:activity-list|activity-create|activity-edit|activity-delete', ['only' => ['index','store']]);
        $this->middleware('permission:activity-create', ['only' => ['create','store']]);
        $this->middleware('permission:activity-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:activity-delete', ['only' => ['destroy']]);
        $this->title = 'Activity';
        $this->slug = route('activity.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = DB::table('activities')->where('deleted_at', '=', NULL);
        if($request->type){
            $response = $response->where('type', '=', $request->type);
            $serach_data['type'] = $request->type;
        }
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
        
        return view('admin.pages.activity.list', compact('rows', 'metadata'));
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
        return view('admin.pages.activity.form', compact('metadata'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:activities,name',
            'price' => 'required',
            'duration' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            Activity::query()->create($data);
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
       
        $details = activity::find($id);
        return view('admin.pages.activity.form', compact('details', 'metadata'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:activities,name,' . $id,
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
            $update = Activity::find($id);
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
        $delete = Activity::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
