<?php

namespace App\Http\Controllers\admin\booking;

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
use App\Models\Ticket;
use App\Models\Customer;
use App\Models\Park;
use App\Models\Park_service;
use App\Models\Service;
use App\Models\Park_activity;
use App\Models\Entry;
use App\Models\Park_entry;
use App\Models\Activity;
use App\Models\TicketActivity;
use App\Models\TicketService;
use App\Models\TicketEntry;
use App\Models\TicketTransaction;
use Validator;

class TicketController extends Controller{

    public function __construct(){
        $this->middleware('permission:ticket-list|ticket-create|ticket-edit|ticket-delete', ['only' => ['index','store']]);
        $this->middleware('permission:ticket-create', ['only' => ['create','store']]);
        $this->middleware('permission:ticket-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:ticket-delete', ['only' => ['destroy']]);
        $this->title = 'Ticket';
        $this->slug = route('ticket.index');
    }

    public function index(Request $request){
        $serach_data = [];
        $response = Ticket::orderBy('id','DESC')->where('deleted_at', '=', NULL);
        if($request->ticket_no){
            $response = $response->where('ticket_no', 'like', '%' . $request->ticket_no . '%');
            $serach_data['ticket_no'] = $request->ticket_no;
        }

        if($request->booking_date){
            $response = $response->where('booking_date', '=', $request->booking_date);
            $serach_data['booking_date'] = $request->booking_date;
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
        return view('admin.pages.ticket.list', compact('rows', 'metadata', 'park'));
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

        $parkEntry = [];
        $parkService = [];
        $parkActivity = [];
        if($request->park_id){
            $parkService = Park_service::query()->select('*')->where('park_id', '=', $request->park_id)->where('is_active', '=', '1')->where('deleted_at', '=', NULL)->get();
            $parkActivity = Park_activity::query()->select('*')->where('park_id', '=', $request->park_id)->where('is_active', '=', '1')->where('deleted_at', '=', NULL)->get();
            $parkEntry = Park_entry::query()->select('*')->where('park_id', '=', $request->park_id)->where('is_active', '=', '1')->where('deleted_at', '=', NULL)->get();
        }
        return view('admin.pages.ticket.form', compact('metadata', 'park', 'parkService', 'parkActivity', 'parkEntry'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'park_id' => 'required', 
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]); 

        if ($validator->fails()) { 
            return redirect()->back()->withInput()->withErrors($validator); 
        }else{

            if($request->park_id > 0 && $request->customer_id){
                $entry_data = $request->entry_data;
                $service_data = $request->service_data;
                $activity_data = $request->activity_data;
                $booking_date = $request->booking_date;
                $park_id = $request->park_id;
                $customer_id = $request->customer_id;

                $ticket_insert_data = array(
                    'booking_date' => $booking_date,
                    'park_id' => $park_id,
                    'customer_id' => $customer_id,
                    'payment_mode' => 1,
                );

                $ticket_inserted = Ticket::create($ticket_insert_data);
                $ticket_id = $ticket_inserted->id;

                //entry
                $entry_sub_total = 0;
                if(!empty($entry_data)){
                    $entry_data = explode(',', $entry_data);
                    foreach ($entry_data as $key => $value) {
                        $entry = explode('-', $value);
                        $sub_total = $entry[1] * $entry[2];
                        $entry_insert_data = [];
                        $entry_insert_data = array(
                            'ticket_id' => $ticket_id,
                            'park_entry_id' => $entry[0],
                            'unit_price' => $entry[1],
                            'quantity' => $entry[2],
                            'sub_total' => $sub_total,
                        );

                        TicketEntry::create($entry_insert_data);
                        $entry_sub_total = $entry_sub_total+$sub_total;
                    }
                }

                //service
                $service_sub_total = 0;
                if(!empty($service_data)){
                    $service_data = explode(',', $service_data);
                    foreach ($service_data as $key => $value) {
                        $service = explode('-', $value);
                        $sub_total = $service[1] * $service[2];
                        $service_insert_data = [];
                        $service_insert_data = array(
                            'ticket_id' => $ticket_id,
                            'park_service_id' => $service[0],
                            'unit_price' => $service[1],
                            'quantity' => $service[2],
                            'sub_total' => $sub_total,
                        );

                        TicketService::create($service_insert_data);
                        $service_sub_total = $service_sub_total+$sub_total;
                    }
                }

                //activity
                $activity_sub_total = 0;
                if(!empty($activity_data)){
                    $activity_data = explode(',', $activity_data);
                    foreach ($activity_data as $key => $value) {
                        $activity = explode('-', $value);
                        $sub_total = $activity[1] * $activity[2];
                        $activity_insert_data = [];
                        $activity_insert_data = array(
                            'ticket_id' => $ticket_id,
                            'park_activity_id' => $activity[0],
                            'unit_price' => $activity[1],
                            'quantity' => $activity[2],
                            'sub_total' => $sub_total,
                        );

                        Ticketactivity::create($activity_insert_data);
                        $activity_sub_total = $activity_sub_total+$sub_total;
                    }
                }

                //transuction 
                $transuction_insert_data = array(
                    'ticket_id' => $ticket_id,
                    'total_payment_amount' => ($service_sub_total + $activity_sub_total),
                    'payment_mode' => 1,
                    'payment_time' => date('Y-m-d H:i:s'),
                    'payment_status' => 1
                );

                TicketTransaction::create($transuction_insert_data);

                //update ticket
                $ticket_update_data = array(
                    'ticket_no' => $park_id.'-'.time().'/'.$ticket_id,
                    'service_sub_total' => $service_sub_total,
                    'activity_sub_total' => $activity_sub_total,
                    'total' => ($service_sub_total + $activity_sub_total),
                );

                $ticket = Ticket::find($ticket_id);
                $ticket->update($ticket_update_data);
                $flash_data = array(
                    'status' => 'success',
                    'message' => $this->title.' successfully booked.',
                );

                Session::put('flash_data', $flash_data); 
                return redirect($this->slug);
            }else{
                $park_id = $request->park_id;
                $booking_date = $request->booking_date;
                $unique_check = Customer::orderBy('id')->where('phone', '=', $request->phone)->where('deleted_at', '=', NULL);
                if($unique_check->first()){
                    $customer_details = $unique_check->first();
                    $customer_id = $customer_details->id;
                }else{
                    $insert_customer_data = array(
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'password' => Hash::make($request->phone),
                    );
                    $insert = Customer::create($insert_customer_data);
                    $customer_id = $insert->id;
                }

                $customer_details = Customer::orderBy('id')->where('id', '=', $customer_id)->where('deleted_at', '=', NULL)->first();
                $customer_data = array(
                    'name' => $customer_details->name,
                    'phone' => $customer_details->phone,
                    'address' => $customer_details->address,
                    'password' => $customer_details->password,
                    'park_id' => $park_id,
                    'booking_date' => $booking_date,
                    'customer_id' => $customer_id,
                );

                $flash_data = $customer_data;
                Session::put('customer_data', $flash_data); 
                return redirect('/admin/adminbooking/ticket/create?park_id='.$park_id.'&customer_id='.$customer_id);
            }
        }
    }

    public function show($id){
        $metadata = array(
            'page_title' => $this->title . ' Details',
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
                    'title' => 'Details',  
                )
            ),
        );


        $ticket = Ticket::orderBy('id')->where('id', '=', $id)->where('deleted_at', '=', NULL)->first();

        $parkService = DB::table('ticket_services')->leftJoin('park_services', 'park_services.id', '=', 'ticket_services.park_service_id')->leftJoin('services', 'services.id', '=', 'park_services.service_id')->where('ticket_services.ticket_id', '=', $id)->get();

        $parkActivity = DB::table('ticket_activities')->leftJoin('park_activities', 'park_activities.id', '=', 'ticket_activities.park_activity_id')->leftJoin('activities', 'activities.id', '=', 'park_activities.activity_id')->where('ticket_activities.ticket_id', '=', $id)->get();

        $parkEntry = DB::table('ticket_entries')->leftJoin('park_entries', 'park_entries.id', '=', 'ticket_entries.park_entry_id')->leftJoin('entries', 'entries.id', '=', 'park_entries.entry_id')->where('ticket_entries.ticket_id', '=', $id)->get();

        return view('admin.pages.ticket.view', compact('metadata', 'ticket', 'parkService', 'parkActivity', 'parkEntry'));
    }

    public function print($id){
        $metadata = array(
            'page_title' => $this->title . ' Details',
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
                    'title' => 'Details',  
                )
            ),
        );


        $ticket = Ticket::orderBy('id')->where('id', '=', $id)->where('deleted_at', '=', NULL)->first();

        $parkService = DB::table('ticket_services')->leftJoin('park_services', 'park_services.id', '=', 'ticket_services.park_service_id')->leftJoin('services', 'services.id', '=', 'park_services.service_id')->where('ticket_services.ticket_id', '=', $id)->get();

        $parkActivity = DB::table('ticket_activities')->leftJoin('park_activities', 'park_activities.id', '=', 'ticket_activities.park_activity_id')->leftJoin('activities', 'activities.id', '=', 'park_activities.activity_id')->where('ticket_activities.ticket_id', '=', $id)->get();

        $parkEntry = DB::table('ticket_entries')->leftJoin('park_entries', 'park_entries.id', '=', 'ticket_entries.park_entry_id')->leftJoin('entries', 'entries.id', '=', 'park_entries.entry_id')->where('ticket_entries.ticket_id', '=', $id)->get();

        return view('admin.pages.ticket.print', compact('metadata', 'ticket', 'parkService', 'parkActivity', 'parkEntry'));
    }

    public function destroy($id){
        $delete = Ticket::find($id);
        $delete->delete();
        $flash_data = array(
            'status' => 'success',
            'message' => $this->title.' successfully deleted.',
        );

        Session::put('flash_data', $flash_data); 
        return redirect($this->slug);
    }
}
