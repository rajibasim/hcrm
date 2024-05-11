<?php

namespace App\Http\Controllers\admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\FineMaster;
use Validator;

class FineMasterController extends Controller{

    public function __construct(){
        $this->middleware('permission:fine-list|fine-create|fine-edit|fine-delete', ['only' => ['index','store']]);
        $this->middleware('permission:fine-create', ['only' => ['create','store']]);
        $this->middleware('permission:fine-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:fine-delete', ['only' => ['destroy']]);
        $this->title = 'Fine Master';
        $this->slug = route('fine.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = DB::table('fine_masters')->where('deleted_at', '=', NULL);
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
        
        return view('admin.pages.fine.list', compact('rows', 'metadata'));
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
        return view('admin.pages.fine.form', compact('metadata'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:fine_masters,name',
            'price' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            FineMaster::query()->create($data);
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
        
        $details = FineMaster::find($id);
        return view('admin.pages.fine.form', compact('details', 'metadata'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:fine_masters,name,' . $id,
            'price' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            $update = FineMaster::find($id);
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
        $delete = FineMaster::find($id);
        $delete = $delete->delete();

        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );
        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
