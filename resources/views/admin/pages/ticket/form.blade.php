@extends('admin.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $metadata['page_title'] }}</h1>
                </div>
                <!-- /.col -->
                @if(isset($metadata['breadcumb']) && $metadata['breadcumb'])
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        @foreach ( $metadata['breadcumb'] as $key => $breadcumb ) 
                        <li class="breadcrumb-item {{ $breadcumb['url'] ? '' : 'active' }}">
                            @if(isset($breadcumb['url']) && $breadcumb['url'])
                            <a href="{{ url($breadcumb['url']) }}">{{ $breadcumb['title'] }}</a>
                            @else
                                {{ $breadcumb['title'] }}
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </div>
                @endif
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- right column -->
          <div class="col-md-12">
            <!-- general form elements disabled -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">{{ $metadata['page_title'] }}</h3>
              </div>
              @if(Session::has('customer_data')) 
                @php 
                  $customer_data = Session::pull('customer_data');
                @endphp
              @endif
              <!-- /.card-header -->
               <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('ticket.update', $details->id) : route('ticket.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <input name="park_id" type="hidden" value="{{ isset($customer_data['park_id']) && $customer_data['park_id'] ? $customer_data['park_id'] : '' }}">
                <input name="customer_id" type="hidden" value="{{ isset($customer_data['customer_id']) && $customer_data['customer_id'] ? $customer_data['customer_id'] : '' }}">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Park</label>
                        <select class="form-control select2" name="park_id" id="park_id" required="">
                          <option value="">Select Park</option>
                          @if($park) && !$park->isEmpty())
                            @foreach ( $park as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($customer_data['park_id']) && $customer_data['park_id'] == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="Name" name="name" id="name" value="{{ isset($customer_data['name']) && $customer_data['name'] ? $customer_data['name'] : '' }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" placeholder="Phone" name="phone" id="phone" value="{{ isset($customer_data['phone']) && $customer_data['phone'] ? $customer_data['phone'] : '' }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" placeholder="Address" name="address" id="address" value="{{ isset($customer_data['address']) && $customer_data['address'] ? $customer_data['address'] : '' }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Booking Date</label>
                        <input type="text" class="form-control datepicker3" placeholder="Booking Date" name="booking_date" id="booking_date" value="{{ isset($customer_data['booking_date']) && $customer_data['booking_date'] ? $customer_data['booking_date'] : '' }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-12" style="display: {{ isset($customer_data) && $customer_data ? '' : 'none' }}">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th colspan="4" style="text-align: center;">Entry</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(isset($parkEntry) && count($parkEntry) > 0)
                            @foreach ( $parkEntry as $key => $res )
                              <tr> 
                                <td style="display: none;">{{ $res->id }}</td>
                                <td width="60%">{{ $res->entry->name }}</td>
                                <td width="10%">{{ $res->price }}</td>
                                <td width="10%">
                                  <select style="width: 60px;" name="entry_qty" class="entry_qty">
                                    @for ($i=0; $i < 500 ; $i++) { 
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                  </select>
                                </td>
                                <td width="20%" class="esub_total">0.00</td>
                              </tr>
                            @endforeach
                              <tr> 
                                <td colspan="3">Activity Total</td>
                                <td id="entry_total">0.00</td>
                              </tr>
                          @else
                            <tr> 
                              <td colspan="4">No entry data avalible.</td>
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-12" style="display: {{ isset($customer_data) && $customer_data ? '' : 'none' }}">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th colspan="4" style="text-align: center;">Services</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(isset($parkService) && count($parkService) > 0)
                            @foreach ( $parkService as $key => $res )
                              <tr> 
                                <td style="display: none;">{{ $res->id }}</td>
                                <td width="60%">{{ $res->service->name }}</td>
                                <td width="10%">{{ $res->price }}</td>
                                <td width="10%">
                                  <select style="width: 60px;" name="serv_qty" class="serv_qty">
                                    @for ($i=0; $i < 500 ; $i++) { 
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                  </select>
                                </td>
                                <td width="20%" class="ssub_total">0.00</td>
                              </tr>
                            @endforeach
                              <tr> 
                                <td colspan="3">Service Total</td>
                                <td id="service_total">0.00</td>
                              </tr>
                          @else
                            <tr> 
                              <td colspan="4">No service avalible.</td>
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-12" style="display: {{ isset($customer_data) && $customer_data ? '' : 'none' }}">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th colspan="4" style="text-align: center;">Activity</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(isset($parkActivity) && count($parkActivity) > 0)
                            @foreach ( $parkActivity as $key => $res )
                              <tr> 
                                <td style="display: none;">{{ $res->id }}</td>
                                <td width="60%">{{ $res->activity->name }}</td>
                                <td width="10%">{{ $res->price }}</td>
                                <td width="10%">
                                  <select style="width: 60px;" name="activ_qty" class="activ_qty">
                                    @for ($i=0; $i < 500 ; $i++) { 
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                  </select>
                                </td>
                                <td width="20%" class="asub_total">0.00</td>
                              </tr>
                            @endforeach
                              <tr> 
                                <td colspan="3">Activity Total</td>
                                <td id="activity_total">0.00</td>
                              </tr>
                          @else
                            <tr> 
                              <td colspan="4">No activity avalible.</td>
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-12" style="display: {{ isset($customer_data) && $customer_data ? '' : 'none' }}">
                      <table class="table table-bordered">
                        <tbody>
                          <tr> 
                            <td width="80%">Total</td>
                            <td id="total">0.00</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <input type="hidden" id="entry_data" name="entry_data" value="">
                <input type="hidden" id="service_data" name="service_data" value="">
                <input type="hidden" id="activity_data" name="activity_data" value="">
                <!-- /.card-body -->
                <div class="card-footer" style="display: {{ isset($customer_data) && $customer_data ? 'none' : '' }}">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ route('ticket.index') }}" class="btn btn-danger">Reset</a>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection

@section('javascripts')
<script type="text/javascript">
$(document).ready(function() {
    @if($errors->any())
      @foreach ($errors->all() as $error)
        @php
        $errors = $error;
        @endphp
      @endforeach
      toastr.error("{{ $errors }}");
    @endif

    $('#dataForm').validate({
      rules: {
          name: {
            required: true,
          },
          price: {
            required: true,
            number: true
          },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
    });

    var serviceArray = [];
    $(".serv_qty").change(function(){
        var ssub_total = 0;
        var total = 0;
        var price = parseFloat($(this).closest('td').prev('td').text());
        var id = parseInt($(this).closest('td').prev('td').prev('td').prev('td').text());
        var qty = parseInt($(this).val());
        var sub_total = (price*qty);

        var service_data = id+'-'+price+'-'+qty;
        serviceArray.push(service_data)
        $("#service_data").val(serviceArray);

        $(this).closest('td').next('td').text(sub_total.toFixed(2));
        $('.ssub_total').each(function() {
            ssub_total = ssub_total + parseFloat($(this).text());
        });
        $("#service_total").text(ssub_total.toFixed(2));
        total = parseFloat($("#entry_total").text()) + parseFloat($("#service_total").text()) + parseFloat($("#activity_total").text());
        $("#total").text(total.toFixed(2));
        if(total > 0){
          $(".card-footer").show();
        }else{
          $(".card-footer").hide();
        }
    });

    var activityAarray = [];
    $(".activ_qty").change(function(){
        var asub_total = 0;
        var total = 0;
        var price = parseFloat($(this).closest('td').prev('td').text());
        var id = parseInt($(this).closest('td').prev('td').prev('td').prev('td').text());
        var qty = parseInt($(this).val());
        var sub_total = (price*qty);

        var activity_data = id+'-'+price+'-'+qty;
        activityAarray.push(activity_data)
        $("#activity_data").val(activityAarray);

        $(this).closest('td').next('td').text(sub_total.toFixed(2));
        $('.asub_total').each(function() {
            asub_total = asub_total + parseFloat($(this).text());
        });
        $("#activity_total").text(asub_total.toFixed(2));
        total = parseFloat($("#entry_total").text()) + parseFloat($("#service_total").text()) + parseFloat($("#activity_total").text());
        $("#total").text(total.toFixed(2));
        if(total > 0){
          $(".card-footer").show();
        }else{
          $(".card-footer").hide();
        }
    });

    var entryArray = [];
    $(".entry_qty").change(function(){
        var esub_total = 0;
        var total = 0;
        var price = parseFloat($(this).closest('td').prev('td').text());
        var id = parseInt($(this).closest('td').prev('td').prev('td').prev('td').text());
        var qty = parseInt($(this).val());
        var sub_total = (price*qty);

        var entry_data = id+'-'+price+'-'+qty;
        entryArray.push(entry_data)
        $("#entry_data").val(entryArray);

        $(this).closest('td').next('td').text(sub_total.toFixed(2));
        $('.esub_total').each(function() {
            esub_total = esub_total + parseFloat($(this).text());
        });
        $("#entry_total").text(esub_total.toFixed(2));
        total = parseFloat($("#entry_total").text()) + parseFloat($("#service_total").text()) + parseFloat($("#activity_total").text());
        $("#total").text(total.toFixed(2));
        if(total > 0){
          $(".card-footer").show();
        }else{
          $(".card-footer").hide();
        }
    });

    
});
</script>
@endsection
