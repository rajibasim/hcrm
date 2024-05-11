<?php

namespace App\Http\Controllers\admin\park;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Models\Park_gate;
use App\Models\Park;
use Validator;

class ParkGateController extends Controller{

    public function __construct(){
        $this->middleware('permission:park_gate-list|park_gate-create|park_gate-edit|park_gate-delete', ['only' => ['index','store']]);
        $this->middleware('permission:park_gate-create', ['only' => ['create','store']]);
        $this->middleware('permission:park_gate-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:park_gate-delete', ['only' => ['destroy']]);
        $this->title = 'Park Gate';
        $this->slug = route('park_gate.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Park_gate::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
        }

        if($request->park_id){
            $response = $response->where('park_id', '=', $request->park_id);
            $serach_data['park_id'] = $request->park_id;
        }

        if(Auth::user()->park_id){
            $response = $response->where('park_id', '=', Auth::user()->park_id);
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

        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.park_gate.list', compact('rows', 'metadata', 'park'));
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

        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.park_gate.form', compact('metadata', 'park'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:park_gates,name',
            'park_id' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all(); 
            Park_gate::create($data);
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

        $details = Park_gate::find($id);
        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.park_gate.form', compact('details', 'metadata', 'park'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:park_gates,name,' . $id,
            'park_id' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $gate = Park_gate::find($id);
            $gate->update($data);
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully updated.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    public function destroy($id){
        $delete = Park_gate::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
