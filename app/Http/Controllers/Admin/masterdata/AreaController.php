<?php

namespace App\Http\Controllers\Admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Area;
use App\Models\Beat;
use Validator;

class AreaController extends Controller{

    public function __construct(){
        $this->middleware('permission:area_list|area_create|area_edit|area_delete', ['only' => ['index','store']]);
        $this->middleware('permission:area_create', ['only' => ['create','store']]);
        $this->middleware('permission:area_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:area_delete', ['only' => ['destroy']]);
        $this->title = 'Area';
        $this->slug = route('area.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];

        $response = Area::with('beats')->where('deleted_at', '=', NULL);
        if($request->area){
            $response = $response->where('area', 'like', '%' . $request->area . '%');
            $serach_data['area'] = $request->area;
        }

        if($request->beat_id){
            $response = $response->where('beat_id', '=', $request->beat_id);
            $serach_data['beat_id'] = $request->beat_id;
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

        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        return view('admin.pages.area.list', compact('rows', 'metadata', 'beat'));
    }

    ### Create View
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
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        return view('admin.pages.area.form', compact('metadata', 'beat'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'beat_id' => 'required',
            'area' => ['required', 'unique:areas,area,NULL,id,beat_id,'.$request->input('beat_id')],
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['created_by'] = created_by();
            $data['updated_by'] = updated_by();
            $created = Area::query()->create($data);
            if($created){
                $flash_data = array(
                    'status' => 'success',
                    'message' => $this->title.' successfully created.',
                );
            }else{
                $flash_data = array(
                    'status' => 'error',
                    'message' => 'Something went wrong, try again.',
                );
            }

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    ### Edit View
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
        
        $details = Area::find($id);
        $beat = Beat::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('beat', 'asc')->get();
        return view('admin.pages.area.form', compact('details', 'metadata', 'beat'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'beat_id' => 'required',
            'area' => ['required', 'unique:areas,area,'.$id.',id,beat_id,'.$request->input('beat_id')],
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['updated_by'] = updated_by();
            $update = Area::find($id);
            $update = $update->update($data);
            if($update){
                $flash_data = array(
                    'status' => 'success',
                    'message' => $this->title.' successfully updated.',
                );
            }else{
                $flash_data = array(
                    'status' => 'error',
                    'message' => 'Something went wrong, try again.',
                );
            }
        
            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    ### Delete Data
    public function destroy($id){
        $delete = Area::find($id);
        $delete = $delete->delete();

        if($delete){
            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully deleted.',
            );
        }else{
            $flash_data = array(
                'status' => 'error',
                'message' => 'Something went wrong, try again.',
            );
        }
        
        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
