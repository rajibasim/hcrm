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
use App\Models\Notice;
use App\Models\Park;
use Validator;

class NoticeController extends Controller{

    public function __construct(){
        $this->middleware('permission:notice-list|notice-create|notice-edit|notice-delete', ['only' => ['index','store']]);
        $this->middleware('permission:notice-create', ['only' => ['create','store']]);
        $this->middleware('permission:notice-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:notice-delete', ['only' => ['destroy']]);
        $this->title = 'Notice';
        $this->slug = route('notice.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Notice::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->title){
            $response = $response->where('title', 'like', '%' . $request->title . '%');
            $serach_data['title'] = $request->title;
        }

        if($request->post_date){
            $response = $response->where('post_date', '=', $request->post_date);
            $serach_data['post_date'] = $request->post_date;
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
        return view('admin.pages.notice.list', compact('rows', 'metadata', 'park'));
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
        return view('admin.pages.notice.form', compact('metadata', 'park'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'title' => 'required|unique:notices,title',
            'park_id' => 'required',
            'post_date' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            if($request->hasFile('notice_file')){
                $fileName = time().'_'.$request->notice_file->getClientOriginalName();
                $request->file('notice_file')->storeAs('notice_file', $fileName, 'public');
                $data['notice_file'] = $fileName;
            }

            $data = $request->all();
            Notice::create($data);
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

        $details = Notice::find($id);
        $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->orderBy('name','ASC')->get();
        if(Auth::user()->park_id){
            $park = Park::query()->select('id', 'name')->where('deleted_at', '=', NULL)->where('is_active', '=', '1')->where('id', '=', Auth::user()->park_id)->orderBy('name','ASC')->get();
        }
        return view('admin.pages.notice.form', compact('details', 'metadata', 'park'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'title' => 'required|unique:notices,title,' . $id,
            'park_id' => 'required',
            'post_date' => 'required',
            'description' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            if($request->hasFile('notice_file')){
                $fileName = time().'_'.$request->notice_file->getClientOriginalName();
                $request->file('notice_file')->storeAs('notice_file', $fileName, 'public');
                $data['notice_file'] = $fileName;
            }
        
            $notice = Notice::find($id);
            $notice->update($data);
           
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully updated.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    public function destroy($id){
        $delete = Notice::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
