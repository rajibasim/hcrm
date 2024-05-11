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
use App\Models\Park_activity;
use App\Models\Park;
use App\Models\Activity;
use Validator;

class ParkActivityController extends Controller{

    public function __construct(){
        $this->middleware('permission:park_activity-list|park_activity-create|park_activity-edit|park_activity-delete', ['only' => ['index','store']]);
        $this->middleware('permission:park_activity-create', ['only' => ['create','store']]);
        $this->middleware('permission:park_activity-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:park_activity-delete', ['only' => ['destroy']]);
        $this->title = 'Park Activity';
        $this->slug = route('park_activity.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Park_activity::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->activity_id){
            $response = $response->where('activity_id', '=', $request->activity_id);
            $serach_data['activity_id'] = $request->activity_id;
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
        $activity = Activity::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        return view('admin.pages.park_activity.list', compact('rows', 'metadata', 'park', 'activity'));
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
        $activity = Activity::query()->select('id', 'name', 'price', 'duration')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        return view('admin.pages.park_activity.form', compact('metadata', 'park', 'activity'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'park_id' => 'required',
            'activity_id' => 'required',
            'price' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $unique_check = Park_activity::orderBy('id','DESC')->where('park_id', '=', $request->park_id)->where('activity_id', '=', $request->activity_id)->where('deleted_at', '=', NULL);
            if($unique_check->first()){
                $flash_data = array(
                    'status' => 'error',
                    'message' => $this->title.' already exist.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $data = $request->all(); 
                Park_activity::create($data);
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

        $details = Park_activity::find($id);
        $activity = Activity::query()->select('id', 'name', 'price', 'duration')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.park_activity.form', compact('details', 'metadata', 'park', 'activity'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'park_id' => 'required',
            'activity_id' => 'required',
            'price' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $unique_check = Park_activity::orderBy('id','DESC')->where('park_id', '=', $request->park_id)->where('activity_id', '=', $request->activity_id)->where('deleted_at', '=', NULL)->where('id', '<>', $id);
            if($unique_check->first()){
                $flash_data = array(
                    'status' => 'error',
                    'message' => $this->title.' already exist.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $data = $request->all();
                $activity = Park_activity::find($id);
                $activity->update($data);
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
        $delete = Park_activity::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
