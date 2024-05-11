<?php
  
namespace App\Http\Controllers\Admin;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
  
class LoginController extends Controller{
   
    ### Admin Login
    public function index(){
        if(Auth::check()){
            return redirect()->intended('admin/dashboard');
        }
        return view('admin.pages.login.login');
    }  
      
    ### Admin Registration
    public function registration(){
        return view('admin.registration');
    }
      
    ### Process Login
    public function processLogin(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('admin/dashboard')->withSuccess('You have Successfully loggedin');
        }

        $error_responce = array(
            'status' => 'error',
            'message' => 'Invalid Login Credentials.',
        );
        Session::put('flash_data', $error_responce);
        return redirect()->back();
    }
      
    ### Process Registration
    public function processRegistration(Request $request){  
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
           
        $data = $request->all();
        $check = $this->create($data);
        return redirect("dashboard")->withSuccess('Great! You have Successfully loggedin');
    }
    
    ### Dashboard
    public function dashboard(){
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
    
    ### Save User Data
    public function create(array $data){
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }
    
    ### Logout
    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect('admin/login');
    }
}