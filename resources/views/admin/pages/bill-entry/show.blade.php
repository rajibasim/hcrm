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
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('return-entry.update', $details->id) : route('return-entry.store') }}" autocomplete="off" enctype="multipart/form-data">
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
                        <input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="{{ old('bill_no', isset($details->bill_no) && $details->bill_no ? $details->bill_no : '') }}" required="" disabled="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Date</label>
                        <input type="text" class="form-control datepicker3" placeholder="Date" name="return_date" value="{{ old('return_date', isset($details->return_date) && $details->return_date ? $details->return_date : '') }}" required="" disabled="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Sales Person</label>
                        <select class="form-control select2" name="sales_person_id" id="sales_person_id" required="" disabled="">
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
                        <select class="form-control select2" name="beat_id" id="beat_id" required="" disabled="">
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
                        <select class="form-control select2" name="area_id" id="area_id" required="" disabled="">
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
                        <select class="form-control select2" name="customer_id" id="customer_id" required="" disabled="">
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
                    <div class="col-sm-6">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Note</label>
                        <input type="text" class="form-control" placeholder="Note" name="note" value="{{ old('note', isset($details->note) && $details->note ? $details->note : '') }}" required="" disabled="">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <table class="table table-bordered">
                         <thead>
                            <tr>
                               <th colspan="5" style="text-align: center;">Product Entry</th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr>
                              <td>Product </td>
                              <td>QTY</td>
                              <td>Unit Price</td>
                              <td>Sub Total</td>
                            </tr>
                            @if(isset($productReturn) && !$productReturn->isEmpty())
                              @foreach ( $productReturn as $rkey => $rres )
                                <tr class="addMore">
                                  <td>
                                    <select class="form-control select2" name="product_id[]" required="" disabled="">
                                      <option value="">Select Product</option>
                                      @if(isset($product)) && !$product->isEmpty())
                                        @foreach ( $product as $key => $res )
                                          <option value="{{ $res->id }}" {{ isset($rres->product_id) && $rres->product_id == $res->id ? 'selected' : '' }}>{{ $res->name }}-{{ $res->unit->unit }}-{{ $res->category->category }}</option>
                                        @endforeach
                                      @endif
                                    </select>
                                  </td>
                                  <td>
                                    <input class="form-control product_qty" placeholder="QTY" type="number" name="product_qty[]" required="" value="{{ isset($rres->product_qty) && $rres->product_qty ? $rres->product_qty : '0' }}" disabled="">
                                    <input type="hidden" class="product_qty_hidden" name="product_qty_hidden[]" value="{{ isset($rres->product_qty) && $rres->product_qty ? $rres->product_qty : '0' }}">
                                  </td>
                                  <td>
                                    <input class="form-control product_unit_price" placeholder="Unit Price" type="number" name="product_unit_price[]" required="" value="{{ isset($rres->product_unit_price) && $rres->product_unit_price ? $rres->product_unit_price : '0' }}" disabled="">
                                    <input type="hidden" class="product_unit_price_hidden" name="product_unit_price_hidden[]" value="{{ isset($rres->product_unit_price) && $rres->product_unit_price ? $rres->product_unit_price : '0' }}">
                                  </td>
                                  <td>
                                    <input class="form-control sub_total" placeholder="Sub Total" type="text" name="sub_total[]" readonly="" value="{{ isset($rres->sub_total) && $rres->sub_total ? $rres->sub_total : '0.00' }}" disabled="">
                                    <input type="hidden" class="sub_total_hidden" name="sub_total_hidden[]" value="{{ isset($rres->sub_total) && $rres->sub_total ? $rres->sub_total : '0' }}">
                                  </td>
                                </tr>
                              @endforeach
                            @else
                              <tr class="addMore">
                                <td width="55%">
                                  <select class="form-control select2" name="product_id[]" required="">
                                    <option value="">Select Product</option>
                                    @if(isset($product)) && !$product->isEmpty())
                                      @foreach ( $product as $key => $res )
                                        <option value="{{ $res->id }}">{{ $res->name }}-{{ $res->unit->unit }}-{{ $res->category->category }}</option>
                                      @endforeach
                                    @endif
                                  </select>
                                </td>
                                <td width="15%">
                                  <input class="form-control product_qty" placeholder="QTY" type="number" name="product_qty[]" required="">
                                  <input type="hidden" class="product_qty_hidden" name="product_qty_hidden[]" value="0">
                                </td>
                                <td width="15%">
                                  <input class="form-control product_unit_price" placeholder="Unit Price" type="number" name="product_unit_price[]" required="">
                                  <input type="hidden" class="product_unit_price_hidden" name="product_unit_price_hidden[]" value="0">
                                </td>
                                <td width="15%">
                                  <input class="form-control sub_total" placeholder="Sub Total" type="text" name="sub_total[]" readonly="">
                                  <input type="hidden" class="sub_total_hidden" name="sub_total_hidden[]" value="0">
                                </td>
                                <td width="10%">
                                  <a href="javascript:void(0);" class="btn btn-primary btn-sm addMoreBtn" data-toggle="tooltip" data-placement="top" title="Add New">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  </a>
                                </td>
                              </tr>
                            @endif
                            <tr>
                               <td colspan="3">Total Amount</td>
                               <td colspan="2" id="total_amount">{{ isset($details->total_amount) && $details->total_amount ? $details->total_amount : '' }}</td>
                               <input type="hidden" class="total_amount" name="total_amount" value="{{ isset($details->total_amount) && $details->total_amount ? $details->total_amount : '' }}">
                            </tr>
                         </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
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

    $(document).on('keyup', '.product_qty', function(e){
      var product_unit_price = $(this).closest('td').next().find(".product_unit_price_hidden").val();
      var product_qty = $(this).val();
      if(product_qty.length > 0 && product_qty > 0){
        $(this).next('input').val(product_qty);
      }
      var sub_total = calculate(product_unit_price, product_qty);
      $(this).closest('td').next().next().find(".sub_total").val(sub_total.toFixed(2));
      $(this).closest('td').next().next().find(".sub_total_hidden").val(sub_total);
      total();
    });

    $(document).on('keyup', '.product_unit_price', function(){
      var product_qty = $(this).closest('td').prev().find(".product_qty_hidden").val();
      var product_unit_price = $(this).val();
      if(product_unit_price.length > 0 && product_unit_price > 0){
        $(this).next('input').val(product_unit_price);
      }

      var sub_total = calculate(product_unit_price, product_qty);
      $(this).closest('td').next().find(".sub_total").val(sub_total.toFixed(2));
      $(this).closest('td').next().find(".sub_total_hidden").val(sub_total);
      total();
    });


    function calculate(product_unit_price, product_qty){
        var sub_total = parseFloat(product_unit_price) * parseFloat(product_qty);
        return sub_total;
    }

    function total(){
      var inputs = $(".sub_total_hidden");
      var total = 0;
      for(var i = 0; i < inputs.length; i++){
          total = total + parseFloat($(inputs[i]).val());
      }

      $("#total_amount").html(total.toFixed(2));
      $(".total_amount").val(total);
    }

    $(".addMoreBtn").click(function () {
      var select = $(".selectOptions").html();
      $(".addMore select").select2('destroy');
      var html = '<tr class="addMore"><td width="45%">'+select+'</td><td width="15%"><input class="form-control product_qty" placeholder="QTY" type="number" name="product_qty[]" required=""><input type="hidden" class="product_qty_hidden" name="product_qty_hidden[]" value="0"></td><td width="15%"><input class="form-control product_unit_price" placeholder="Unit Price" type="number" name="product_unit_price[]" required=""><input type="hidden" class="product_unit_price_hidden" name="product_unit_price_hidden[]" value="0"></td><td width="15%"><input class="form-control sub_total" placeholder="Sub Total" type="text" name="sub_total[]" readonly=""><input type="hidden" class="sub_total_hidden" name="sub_total_hidden[]" value="0"></td><td width="10%"><a href="javascript:void(0);" class="btn btn-danger btn-sm remove" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-minus" aria-hidden="true"></i></a></td></tr>';
        //$('input').rules('add', 'required');
        $(".addMore").last().after(html);
        $(".addMore select").select2({theme: 'bootstrap4'});
    });

    $(document).on('click', '.remove', function(e){
      e.preventDefault();
      $(this).parent().parent().remove();
      total();
    });   
});
</script>
@endsection
