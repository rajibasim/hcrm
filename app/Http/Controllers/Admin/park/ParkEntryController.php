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
use App\Models\Park_entry;
use App\Models\Park;
use App\Models\Entry;
use Validator;

class ParkentryController extends Controller{

    public function __construct(){
        $this->middleware('permission:park_entry-list|park_entry-create|park_entry-edit|park_entry-delete', ['only' => ['index','store']]);
        $this->middleware('permission:park_entry-create', ['only' => ['create','store']]);
        $this->middleware('permission:park_entry-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:park_entry-delete', ['only' => ['destroy']]);
        $this->title = 'Park Eentry';
        $this->slug = route('park_entry.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Park_entry::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->entry_id){
            $response = $response->where('entry_id', '=', $request->entry_id);
            $serach_data['entry_id'] = $request->entry_id;
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
        $entry = Entry::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        return view('admin.pages.park_entry.list', compact('rows', 'metadata', 'park', 'entry'));
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
        $entry = Entry::query()->select('id', 'name', 'price', 'duration')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        return view('admin.pages.park_entry.form', compact('metadata', 'park', 'entry'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'park_id' => 'required',
            'entry_id' => 'required',
            'price' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $unique_check = Park_entry::orderBy('id','DESC')->where('park_id', '=', $request->park_id)->where('entry_id', '=', $request->entry_id)->where('deleted_at', '=', NULL);
            if($unique_check->first()){
                $flash_data = array(
                    'status' => 'error',
                    'message' => $this->title.' already exist.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $data = $request->all(); 
                Park_entry::create($data);
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

        $details = Park_entry::find($id);
        $entry = Entry::query()->select('id', 'name', 'price', 'duration')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','DESC')->get();
        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.park_entry.form', compact('details', 'metadata', 'park', 'entry'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'park_id' => 'required',
            'entry_id' => 'required',
            'price' => 'required',
            'note' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $unique_check = Park_entry::orderBy('id','DESC')->where('park_id', '=', $request->park_id)->where('entry_id', '=', $request->entry_id)->where('deleted_at', '=', NULL)->where('id', '<>', $id);
            if($unique_check->first()){
                $flash_data = array(
                    'status' => 'error',
                    'message' => $this->title.' already exist.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $data = $request->all();
                $entry = Park_entry::find($id);
                $entry->update($data);
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
        $delete = Park_entry::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
