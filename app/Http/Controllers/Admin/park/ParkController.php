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
use App\Models\Park;
use App\Models\Park_entry;
use App\Models\Entry;
use Validator;

class ParkController extends Controller{

    public function __construct(){
        $this->middleware('permission:park-list|park-create|park-edit|park-delete', ['only' => ['index','store']]);
        $this->middleware('permission:park-create', ['only' => ['create','store']]);
        $this->middleware('permission:park-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:park-delete', ['only' => ['destroy']]);
        $this->title = 'Park';
        $this->slug = route('parks.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Park::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
        }

        if($request->email){
            $response = $response->where('contact_email', 'like', '%' . $request->email . '%');
            $serach_data['email'] = $request->email;
        }

        if($request->phone){
            $response = $response->where('contact_phone', '=', $request->phone);
            $serach_data['phone'] = $request->phone;
        }

        if($request->zip_code){
            $response = $response->where('zip_code', '=', $request->zip_code);
            $serach_data['zip_code'] = $request->zip_code;
        }

        if(Auth::user()->park_id){
            $response = $response->where('id', '=', Auth::user()->park_id);
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

        return view('admin.pages.park.list', compact('rows', 'metadata'));
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

        return view('admin.pages.park.form', compact('metadata'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:parks,name',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'banner' => 'required',
            'contact_email' => 'required',
            'contact_phone' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            if($request->hasFile('banner')){
                $fileName = time().'_'.$request->banner->getClientOriginalName();
                $request->file('banner')->storeAs('banner', $fileName, 'public');
                $data['banner'] = $fileName;
            }

            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            $park = Park::create($data);
            //entry asign with park
            $park_id = $park->id;
            $entry = Entry::query()->select('id', 'price')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('id','asc')->get();
            foreach ($entry as $key => $value) {
                $entry_insert_data = [];
                $entry_insert_data = array(
                    'park_id' => $park_id,
                    'entry_id' => $value->id,
                    'price' => $value->price,
                );

                Park_entry::create($entry_insert_data);
            }

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

        $details = park::find($id);
        return view('admin.pages.park.form', compact('details', 'metadata'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:parks,name,' . $id,
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'banner' => 'required',
            'contact_email' => 'required',
            'contact_phone' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            if($request->hasFile('banner')){
                $fileName = time().'_'.$request->banner->getClientOriginalName();
                $request->file('banner')->storeAs('banner', $fileName, 'public');
                $data['banner'] = $fileName;
            }
        
            $park = Park::find($id);
            $park->update($data);
           
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully updated.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    public function destroy($id){
        $delete = Park::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }


    public function show($id){
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

        $details = park::find($id);
        return view('admin.pages.park.view', compact('details', 'metadata'));
    }
}
