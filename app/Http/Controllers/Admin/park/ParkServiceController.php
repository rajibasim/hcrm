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
use App\Models\Park_service;
use App\Models\Park;
use App\Models\Service;
use Validator;

class ParkServiceController extends Controller{

    public function __construct(){
        $this->middleware('permission:park_service-list|park_service-create|park_service-edit|park_service-delete', ['only' => ['index','store']]);
        $this->middleware('permission:park_service-create', ['only' => ['create','store']]);
        $this->middleware('permission:park_service-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:park_service-delete', ['only' => ['destroy']]);
        $this->title = 'Park Service';
        $this->slug = route('park_service.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Park_service::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->service_id){
            $response = $response->where('service_id', '=', $request->service_id);
            $serach_data['service_id'] = $request->service_id;
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
        $service = Service::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        return view('admin.pages.park_service.list', compact('rows', 'metadata', 'park', 'service'));
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
        $service = service::query()->select('id', 'name', 'price', 'duration')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        return view('admin.pages.park_service.form', compact('metadata', 'park', 'service'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'park_id' => 'required',
            'service_id' => 'required',
            'price' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $unique_check = Park_service::orderBy('id','DESC')->where('park_id', '=', $request->park_id)->where('service_id', '=', $request->service_id)->where('deleted_at', '=', NULL);
            if($unique_check->first()){
                $flash_data = array(
                    'status' => 'error',
                    'message' => $this->title.' already exist.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $data = $request->all(); 
                Park_service::create($data);
                $flash_data = array(
                    'status' => 'success',
                    'message' => $this->title.' successfully created.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }
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

        $details = Park_service::find($id);
        $service = service::query()->select('id', 'name', 'price', 'duration')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.park_service.form', compact('details', 'metadata', 'park', 'service'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'park_id' => 'required',
            'service_id' => 'required',
            'price' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $unique_check = Park_service::orderBy('id','DESC')->where('park_id', '=', $request->park_id)->where('service_id', '=', $request->service_id)->where('deleted_at', '=', NULL)->where('id', '<>', $id);
            if($unique_check->first()){
                $flash_data = array(
                    'status' => 'error',
                    'message' => $this->title.' already exist.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $data = $request->all();
                $service = Park_service::find($id);
                $service->update($data);
                $flash_data = array(
                    'status' => 'success',
                    'message' => $this->title.' successfully updated.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }
        }
    }

    public function destroy($id){
        $delete = Park_service::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
