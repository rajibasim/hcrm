@extends('admin.layouts.app')

@section('content')
<style type="text/css">
  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox */
  input[type=number] {
    -moz-appearance: textfield;
  }
</style>
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
              <!-- /.card-header -->
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('bill-entry.update', $details->id) : route('bill-entry.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Bill No</label>
                        <input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="{{ old('bill_no', isset($details->bill_no) && $details->bill_no ? $details->bill_no : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Date</label>
                        <input type="text" class="form-control datepicker3" placeholder="Date" name="return_date" value="{{ old('return_date', isset($details->return_date) && $details->return_date ? $details->return_date : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Sales Person</label>
                        <select class="form-control select2" name="sales_person_id" id="sales_person_id" required="">
                          <option value="">Select Sales Person</option>
                          @if(isset($sales_person)) && !$sales_person->isEmpty())
                            @foreach ( $sales_person as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($details->sales_person_id) && $details->sales_person_id == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Beat</label>
                        <select class="form-control select2" name="beat_id" id="beat_id" required="">
                          <option value="">Select Beat</option>
                          @if(isset($beat)) && !$beat->isEmpty())
                            @foreach ( $beat as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($details->beat_id) && $details->beat_id == $res->id ? 'selected' : '' }}>{{ $res->beat }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Area</label>
                        <select class="form-control select2" name="area_id" id="area_id" required="">
                          <option value="">Select Area</option>
                          @if(isset($details->area_id) && $details->area_id)
                            @if(isset($area)) && !$area->isEmpty())
                              @foreach ( $area as $key => $res )
                                <option value="{{ $res->id }}" {{ isset($details->area_id) && $details->area_id == $res->id ? 'selected' : '' }}>{{ $res->area }}</option>
                              @endforeach
                            @endif
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Customer</label>
                        <select class="form-control select2" name="customer_id" id="customer_id" required="">
                          <option value="">Select Customer</option>
                          @if(isset($details->customer_id) && $details->customer_id)
                            @if(isset($customer)) && !$customer->isEmpty())
                              @foreach ( $customer as $key => $res )
                                <option value="{{ $res->id }}" {{ isset($details->customer_id) && $details->customer_id == $res->id ? 'selected' : '' }}>{{ $res->store_name }}</option>
                              @endforeach
                            @endif
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Total Amount</label>
                        <input type="number" class="form-control" placeholder="Total Amount" name="total_amount" id="total_amount" value="{{ old('total_amount', isset($details->total_amount) && $details->total_amount ? $details->total_amount : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Online</label>
                        <input type="number" class="form-control" placeholder="Online" name="online_amount" id="online_amount" value="{{ old('online_amount', isset($details->online_amount) && $details->online_amount ? $details->online_amount : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Offline</label>
                        <input type="number" class="form-control" placeholder="Offline" name="offline_amount" id="offline_amount" value="{{ old('offline_amount', isset($details->offline_amount) && $details->offline_amount ? $details->offline_amount : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Balance</label>
                        <input type="text" class="form-control" placeholder="Balance" name="balance_amount" id="balance_amount" value="{{ old('balance_amount', isset($details->balance_amount) && $details->balance_amount ? $details->balance_amount : '') }}" readonly="">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Note</label>
                        <input type="text" class="form-control" placeholder="Note" name="note" value="{{ old('note', isset($details->note) && $details->note ? $details->note : '') }}">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ route('product.index') }}" class="btn btn-danger">Reset</a>
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

<div class="selectOptions" style="display: none;">
  <select class="form-control" name="product_id[]" required="">
    <option value="">Select Product</option>
    @if(isset($product)) && !$product->isEmpty())
      @foreach ( $product as $key => $res )
        <option value="{{ $res->id }}">{{ $res->name }}-{{ $res->unit->unit }}-{{ $res->category->category }}</option>
      @endforeach
    @endif
  </select>
</div>
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
          unit_id: {
            required: true,
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

    $("#beat_id").change(function(){
       var beat_id = $(this).val();
       var areas = '{{ json_encode($area) }}';
       areas = JSON.parse(areas.replace(/&quot;/g,'"'));
       $("#area_id").select2('destroy'); 
       $("#customer_id").select2('destroy'); 
       $("#customer_id").html('<option value="">Select Customer</option>');
       $("#customer_id").select2({theme: 'bootstrap4'});
       var html = '<option value="">Select Area</option>';
       $.each(areas, function (key, val) {
          if(beat_id == val.beat_id){
            html = html + '<option value="'+val.id+'">'+val.area+'</option>';
          }
       });
       $("#area_id").html(html);
       $("#area_id").select2({theme: 'bootstrap4'});
    });

    $("#area_id").change(function(){
       var beat_id = $("#beat_id").val();
       var area_id = $(this).val();
       var customer = '{{ json_encode($customer) }}';
       customer = JSON.parse(customer.replace(/&quot;/g,'"'));
       $("#customer_id").select2('destroy'); 
       var html = '<option value="">Select Customer</option>';
       $.each(customer, function (key, val) {
          if(beat_id == val.beat_id && area_id == val.area_id){
            html = html + '<option value="'+val.id+'">'+val.store_name+'</option>';
          }
       });
       $("#customer_id").html(html);
       $("#customer_id").select2({theme: 'bootstrap4'});
    });  

    $(document).on('keyup', '#total_amount', function(e){      
      total();
    });

    $(document).on('keyup', '#online_amount', function(e){
      total();
    });

    $(document).on('keyup', '#offline_amount', function(e){      
      total();
    });

    

    function total(){
      var total_amount = parseFloat($("#total_amount").val());
      var online_amount = parseFloat($("#online_amount").val());
      var offline_amount = parseFloat($("#offline_amount").val());

      if(total_amount.length > 0 || online_amount.length > 0 || offline_amount > 0){
        total_amount = total_amount;
        online_amount = online_amount;
        offline_amount = offline_amount;
      }else{
        total_amount = 0; 
        online_amount = 0;
        offline_amount = 0;
      }

      var balance = total_amount - (online_amount + offline_amount);
      $("#balance_amount").val(balance);
    }

    


});
</script>
@endsection
