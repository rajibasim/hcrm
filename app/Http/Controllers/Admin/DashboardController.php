<?php
  
namespace App\Http\Controllers\Admin;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
  
class DashboardController extends Controller{
   
    ### Dashboard
    public function index(){
        $metadata = array(
            'page_title' => 'Dashboard'
        );
        if(Auth::check()){
            return view('admin.pages.dashboard.dashboard')->with('metadata', $metadata);
        }
        $error_responce = array(
            'status' => 'error',
            'message' => 'Invalid Login Credentials.',
        );
        Session::put('flash_data', $error_responce);
        return redirect("admin/login");
    }
}