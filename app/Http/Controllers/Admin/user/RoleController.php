<?php

namespace App\Http\Controllers\admin\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;
use Validator;

class RoleController extends Controller{

    public function __construct(){
        $this->middleware('permission:role_view|role_create|role_edit|role_delete', ['only' => ['index','store']]);
        $this->middleware('permission:role_create', ['only' => ['create','store']]);
        $this->middleware('permission:role_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role_delete', ['only' => ['destroy']]);
        $this->title = 'Role';
        $this->slug = route('role.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $rows = Role::orderBy('id','DESC')->paginate(10);

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
        
        return view('admin.pages.role.list', compact('rows', 'metadata'));
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

        $permissionGroup = PermissionGroup::orderByRaw('CONVERT(id, SIGNED) asc')->get();
        $permission = Permission::get();
        $rolePermissions = [];
        return view('admin.pages.role.form', compact('metadata', 'permission', 'rolePermissions', 'permissionGroup'));
    }

    public function store(Request $request){
        $attributes = [
            'name' => 'role name',
        ];
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,label',
            'permission' => 'required|array',
        ], [], $attributes); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{
            $name = trim($request->name);
            $data = [];
            $data['name'] = Str::snake($name);
            $data['label'] = $name;
            $data['created_by'] = created_by();
            $role = Role::create($data);

            if($request->permission){
                $permissions = array_reduce($request->permission, function ($carry, $item) {
                    return array_merge($carry, $item);
                }, []);

                $permissions = Permission::whereIn('name', $permissions)->get();
                $role->syncPermissions($permissions);
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

        $details = Role::find($id);
        $permissionGroup = PermissionGroup::orderByRaw('CONVERT(id, SIGNED) asc')->get();
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('admin.pages.role.form', compact('details', 'metadata', 'permission', 'rolePermissions', 'permissionGroup'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|unique:roles,label,'.$id,
            'permission' => 'required|array',
        ]); 
        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{

            $name = trim($request->name);
            $data = [];
            $data['name'] = Str::snake($name);
            $data['label'] = $name;
            //$data['is_active'] = $request->is_active;
            $data['updated_by'] = updated_by();

            $role = Role::find($id);
            $role->update($data);

            if($request->permission){
                $permissions = array_reduce($request->permission, function ($carry, $item) {
                    return array_merge($carry, $item);
                }, []);

                $permissions = Permission::whereIn('name', $permissions)->get();
                $role->syncPermissions($permissions);
            }

            $flash_data = array(
                'status' => 'success',
                'message' => $this->title.' successfully updated.',
            );

            Session::put('flash_data', $flash_data); 
            return redirect($this->slug);
        }
    }

    public function destroy($id){
        $delete = Role::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
