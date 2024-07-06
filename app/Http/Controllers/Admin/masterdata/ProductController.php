<?php

namespace App\Http\Controllers\Admin\masterdata;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use Validator;

class ProductController extends Controller{

    public function __construct(){
        $this->middleware('permission:product_view|product_create|product_edit|product_delete', ['only' => ['index','store']]);
        $this->middleware('permission:product_create', ['only' => ['create','store']]);
        $this->middleware('permission:product_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product_delete', ['only' => ['destroy']]);
        $this->title = 'Product';
        $this->slug = route('product.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = Product::with('unit')->with('category')->where('deleted_at', '=', NULL);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
        }

        if($request->unit_id){
            $response = $response->where('unit_id', '=', $request->unit_id);
            $serach_data['unit_id'] = $request->unit_id;
        }

        if($request->category_id){
            $response = $response->where('category_id', '=', $request->category_id);
            $serach_data['category_id'] = $request->category_id;
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
        $unit = Unit::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('unit', 'asc')->get();
        $category = Category::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('category', 'asc')->get();
        
        return view('admin.pages.product.list', compact('rows', 'metadata', 'unit', 'category'));
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

        $unit = Unit::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('unit', 'asc')->get();
        $category = Category::where('deleted_at', '=', NULL)->where('is_active', '=', 1)->orderBy('category', 'asc')->get();
        return view('admin.pages.product.form', compact('metadata', 'unit', 'category'));
    }

    ### Store Data
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:products,name',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['created_by'] = created_by();
            $created = Product::query()->create($data);
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
        
        $details = Beat::find($id);
        return view('admin.pages.product.form', compact('details', 'metadata'));
    }

    ### Update Data
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'beat' => 'required|unique:beats,beat,' . $id,
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            $data['updated_by'] = updated_by();
            $update = Beat::find($id);
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
        $delete = Beat::find($id);
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
