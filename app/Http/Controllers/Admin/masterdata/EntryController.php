<?php

namespace App\Http\Controllers\admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Entry;
use Validator;

class EntryController extends Controller{

    public function __construct(){
        $this->middleware('permission:entry-list|entry-create|entry-edit|entry-delete', ['only' => ['index','store']]);
        $this->middleware('permission:entry-create', ['only' => ['create','store']]);
        $this->middleware('permission:entry-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:entry-delete', ['only' => ['destroy']]);
        $this->title = 'Entry';
        $this->slug = route('entry.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = DB::table('entries')->where('deleted_at', '=', NULL);
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
        
        return view('admin.pages.entry.list', compact('rows', 'metadata'));
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
        return view('admin.pages.entry.form', compact('metadata'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:entries,name',
            'price' => 'required',
            'duration' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['slug'] = Str::slug($data['name']);
            Entry::query()->create($data);
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
       
        $details = Entry::find($id);
        return view('admin.pages.entry.form', compact('details', 'metadata'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:entries,name,' . $id,
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
            $update = Entry::find($id);
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
        $delete = entry::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
