<?php

namespace App\Http\Controllers\Admin\return_entry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Area;
use App\Models\Beat;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesPerson;
use App\Models\ReturnEntry;
use App\Models\ReturnEntryProduct;
use Validator;

class ReturnProductController extends Controller{

    public function __construct(){
        $this->middleware('permission:return_product_view|return_product_create|return_product_edit|return_product_delete', ['only' => ['index','store']]);
        $this->middleware('permission:return_product_create', ['only' => ['create','store']]);
        $this->middleware('permission:return_product_edit', ['only' => ['edit','update']]);
        $this->middleware('permission:return_product_delete', ['only' => ['destroy']]);
        $this->title = 'Return Product';
        $this->slug = route('return-product.index');
    }

    ### List View
    public function index(Request $request){
        $serach_data = [];
        $response = ReturnEntryProduct::with('product')->where('deleted_at', '=', NULL);
        if($request->product_id){
            $response = $response->where('product_id', '=', $request->product_id);
            $serach_data['product_id'] = $request->product_id;
        }

        if($request->start_date){
            $response = $response->whereDate('return_date', '>=', $request->start_date);
            $serach_data['start_date'] = $request->start_date;
        }

        if($request->end_date){
            $response = $response->whereDate('return_date', '<=', $request->end_date);
            $serach_data['end_date'] = $request->end_date;
        }

        $sub_total = $response->sum('sub_total');
        $product_qty = $response->sum('product_qty');

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
        $product = Product::with('unit')->with('category')->where('is_active', '=', 1)->where('deleted_at', '=', NULL)->get();
        return view('admin.pages.return-product.list', compact('rows', 'metadata', 'product', 'sub_total', 'product_qty'));
    }
}
