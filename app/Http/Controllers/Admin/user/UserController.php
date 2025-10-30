<?php

namespace App\Http\Controllers\Admin\user;

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
use App\Models\User;
use App\Models\SalesPerson;
use Validator;

class UserController extends Controller{

    public function __construct(){
        $this->middleware('permission:user_view|user_create|user_edit|user_delete', ['only' => ['index','store']]);
        $this->middleware('permission:user_create', ['only' => ['create','store']]);
        $this->middleware('permission:user_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user_delete', ['only' => ['destroy']]);
        $this->title = 'User';
        $this->slug = route('user.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = User::orderBy('id','DESC')->where('deleted_at', '=', NULL)->where('user_type', '=', 1);
        if($request->name){
            $response = $response->where('name', 'like', '%' . $request->name . '%');
            $serach_data['name'] = $request->name;
        }

        if($request->email){
            $response = $response->where('email', 'like', '%' . $request->email . '%');
            $serach_data['email'] = $request->email;
        }

        if($request->phone){
            $response = $response->where('phone', '=', $request->phone);
            $serach_data['phone'] = $request->phone;
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

        return view('admin.pages.user.list', compact('rows', 'metadata'));
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

        $roles = Role::pluck('name','name')->all();
        $userRole = [];
        return view('admin.pages.user.form', compact('metadata', 'roles', 'userRole'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'employee_id' => 'required',
            'password' => 'required',
            'roles' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            if($request->is_sales_person){
                $SalesPerson = array(
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile' => $request->phone,
                    'created_by' => created_by(),
                );

                $salesPerson = SalesPerson::query()->create($SalesPerson);
                $sales_person_id = $salesPerson->id;
                $data['sales_person_id'] = $sales_person_id;
            }

            
            if($request->hasFile('profile_image')){
                $fileName = time().'_'.$request->profile_image->getClientOriginalName();
                $request->file('profile_image')->storeAs('profile_image', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $data['user_type'] = 1;
            $data['is_login'] = 1;
            $data['created_by'] = created_by();
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            $user->assignRole($request->input('roles'));
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

        $roles = Role::pluck('name','name')->all();
        $details = User::find($id);
        $userRole = $details->roles->pluck('name','name')->all();
        return view('admin.pages.user.form', compact('details', 'metadata', 'roles', 'userRole'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
            'employee_id' => 'required',
            'roles' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $data = $request->all();
            if(!empty($input['password'])){ 
                $data['password'] = Hash::make($data['password']);
            }else{
                $data = Arr::except($data,array('password'));    
            }

            if($request->hasFile('profile_image')){
                $fileName = time().'_'.$request->profile_image->getClientOriginalName();
                $request->file('profile_image')->storeAs('profile_image', $fileName, 'public');
                $data['image'] = $fileName;
            }

            $data['updated_by'] = updated_by();
            $user = User::find($id);
            $user->update($data);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
            $user->assignRole($request->input('roles'));

            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully updated.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);            
        }
    }

    public function destroy($id){
        $delete = User::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
