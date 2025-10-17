@extends('admin.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<style type="text/css">
  .select2 {
   width: 100% !important;
  } 
</style>
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
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('bill.update', $details->id) : route('bill.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Bill Number</label>
                        <input type="text" class="form-control" placeholder="Bill Number" name="bill_number" value="{{ old('bill_number', isset($details->bill_number) && $details->bill_number ? $details->bill_number : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Invoice Date </label>
                        <input type="text" class="form-control datepicker2" placeholder="Invoice Date" name="invoice_date" value="{{ old('invoice_date', isset($details->invoice_date) && $details->invoice_date ? $details->invoice_date : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Delivery Date </label>
                        <input type="text" class="form-control datepicker2" placeholder="Delivery Date" name="delivery_status_update_date" value="{{ old('delivery_status_update_date', isset($details->delivery_status_update_date) && $details->delivery_status_update_date ? $details->delivery_status_update_date : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Delivery Status</label>
                        <select class="form-control select2" name="delivery_status_id" id="delivery_status_id" required="">
                          <option value="">Select Delivery Status</option>
                          @if($DeliveryStatus) && !$DeliveryStatus->isEmpty())
                            @foreach ( $DeliveryStatus as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($details->delivery_status_id) && $details->delivery_status_id == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Sales Person</label>
                        <select class="form-control select2" name="sales_person_id" id="sales_person_id" required="">
                          <option value="">Select Sales Person</option>
                          @if($SalesPerson) && !$SalesPerson->isEmpty())
                            @foreach ( $SalesPerson as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($details->sales_person_id) && $details->sales_person_id == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Customer</label>
                        <select class="form-control select2" name="customer_id" id="customer_id" required="">
                          <option value="">Select Customer</option>
                          @if($customer) && !$customer->isEmpty())
                            @foreach ( $customer as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($details->customer_id) && $details->customer_id == $res->id ? 'selected' : '' }}>{{ $res->party_name }}-{{ $res->party_code }}-{{ $res->beat }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Billed Amount</label>
                        <input type="number" class="form-control" placeholder="Billed Amount" name="billed_amount" value="{{ old('billed_amount', isset($details->billed_amount) && $details->billed_amount ? $details->billed_amount : '') }}" required="">
                        <input type="hidden" class="billed_amount_hidden" name="billed_amount_hidden" value="{{ isset($details->billed_amount) && $details->billed_amount ? $details->billed_amount : '0' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Return Amount</label>
                        <input type="number" class="form-control" placeholder="Return Amount" name="return_amount" value="{{ old('return_amount', isset($details->return_amount) && $details->return_amount ? $details->return_amount : '') }}" required="">
                        <input type="hidden" id="return_amount_hidden" name="return_amount_hidden" value="{{ isset($details->return_amount) && $details->return_amount ? $details->return_amount : '0' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Damage Amount</label>
                        <input type="number" class="form-control" placeholder="Damage Amount" name="damage_amount" value="{{ old('damage_amount', isset($details->damage_amount) && $details->damage_amount ? $details->damage_amount : '') }}" required="">
                        <input type="hidden" id="damage_amount_hidden" name="damage_amount_hidden" value="{{ isset($details->damage_amount) && $details->damage_amount ? $details->damage_amount : '0' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Adjusment Percent</label>
                        <input type="number" class="form-control" placeholder="Adjusment Percent" name="adjusment_percent" value="{{ old('adjusment_percent', isset($details->adjusment_percent) && $details->adjusment_percent ? $details->adjusment_percent : '1.75') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Adjusment Amount</label>
                        <input type="number" class="form-control" placeholder="Adjusment Amount" name="adjusment_amount" value="{{ old('adjusment_amount', isset($details->adjusment_amount) && $details->adjusment_amount ? $details->adjusment_amount : '') }}" required="">
                        <input type="hidden" id="adjusment_amount_hidden" name="adjusment_amount_hidden" value="{{ isset($details->adjusment_amount) && $details->adjusment_amount ? $details->adjusment_amount : '0' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Status</label>
                        <select class="form-control select2" name="is_active" id="is_active">
                          <option value="1" {{ isset($details->is_active) && $details->is_active == 1 ? 'selected' : '' }}>Active</option>
                          <option value="0" {{ isset($details->is_active) && $details->is_active == 0 ? 'selected' : '' }}>In-Active</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Note</label>
                        <input type="text" class="form-control" placeholder="Note" name="note" value="{{ old('note', isset($details->note) && $details->note ? $details->note : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <table class="table table-bordered">
                         <thead>
                            <tr>
                               <th colspan="5" style="text-align: center;">Payment History</th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr>
                              <td width="30%">Date </td>
                              <td width="30%">Onile/PhonePe</td>
                              <td width="30%">Offline/Cash</td>
                              <td width="10%">Action</td>
                            </tr>
                            @if(isset($productReturn) && !$productReturn->isEmpty())
                              @foreach ( $productReturn as $rkey => $rres )
                                <tr class="addMore">
                                  <td width="55%">
                                    <select class="form-control select2" name="product_id[]" required="">
                                      <option value="">Select Product</option>
                                      @if(isset($product)) && !$product->isEmpty())
                                        @foreach ( $product as $key => $res )
                                          <option value="{{ $res->id }}" {{ isset($rres->product_id) && $rres->product_id == $res->id ? 'selected' : '' }}>{{ $res->name }}-{{ $res->unit->unit }}-{{ $res->category->category }}</option>
                                        @endforeach
                                      @endif
                                    </select>
                                  </td>
                                  <td width="15%">
                                    <input class="form-control product_qty" placeholder="QTY" type="number" name="product_qty[]" required="" value="{{ isset($rres->product_qty) && $rres->product_qty ? $rres->product_qty : '0' }}">
                                    <input type="hidden" class="product_qty_hidden" name="product_qty_hidden[]" value="{{ isset($rres->product_qty) && $rres->product_qty ? $rres->product_qty : '0' }}">
                                  </td>
                                  <td width="15%">
                                    <input class="form-control product_unit_price" placeholder="Unit Price" type="number" name="product_unit_price[]" required="" value="{{ isset($rres->product_unit_price) && $rres->product_unit_price ? $rres->product_unit_price : '0' }}">
                                    <input type="hidden" class="product_unit_price_hidden" name="product_unit_price_hidden[]" value="{{ isset($rres->product_unit_price) && $rres->product_unit_price ? $rres->product_unit_price : '0' }}">
                                  </td>
                                  <td width="15%">
                                    <input class="form-control cash_amount" placeholder="Sub Total" type="text" name="cash_amount[]" readonly="" value="{{ isset($rres->cash_amount) && $rres->cash_amount ? $rres->cash_amount : '0.00' }}">
                                    <input type="hidden" class="cash_amount_hidden" name="cash_amount_hidden[]" value="{{ isset($rres->cash_amount) && $rres->cash_amount ? $rres->cash_amount : '0' }}">
                                  </td>
                                  @if($rkey > 0)
                                    <td width="10%">
                                      <a href="javascript:void(0);" class="btn btn-danger btn-sm remove"><i class="fa fa-minus" aria-hidden="true"></i></a>
                                    </td>
                                  @else
                                    <td width="10%">
                                      <a href="javascript:void(0);" class="btn btn-primary btn-sm addMoreBtn" data-toggle="tooltip" data-placement="top" title="Add New">
                                      <i class="fa fa-plus" aria-hidden="true"></i>
                                      </a>
                                    </td>
                                  @endif
                                </tr>
                              @endforeach
                            @else
                              <tr class="addMore">
                                <td width="30%" style="width: 10px;">
                                  <input class="form-control datepicker2" placeholder="Date" type="text" name="payment_date[]" required="">
                                </td>
                                <td width="30%">
                                  <input class="form-control online_amount" placeholder="Onile/PhonePe" type="number" name="online_amount[]" required="">
                                  <input type="hidden" class="online_amount_hidden" name="online_amount_hidden[]" value="0">
                                </td>
                                <td width="30%">
                                  <input class="form-control cash_amount" placeholder="Offline/Cash" type="number" name="cash_amount[]" required="">
                                  <input type="hidden" class="cash_amount_hidden" name="cash_amount_hidden[]" value="0">
                                </td>
                                <td width="10%">
                                  <a href="javascript:void(0);" class="btn btn-primary btn-sm addMoreBtn" data-toggle="tooltip" data-placement="top" title="Add New">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  </a>
                                </td>
                              </tr>
                            @endif
                            <tr>
                               <td colspan="3">Balance Amount</td>
                               <td colspan="2" id="balance_amount">{{ isset($details->balance_amount) && $details->balance_amount ? $details->balance_amount : '0.00' }}</td>
                               <input type="hidden" class="balance_amount" name="balance_amount" value="{{ isset($details->balance_amount) && $details->balance_amount ? $details->balance_amount : '' }}">
                            </tr>
                         </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ route('customer.index') }}" class="btn btn-danger">Reset</a>
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
          bill_number: {
            required: true,
          },
          invoice_date: {
            required: true,
            number: true,
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


    $(".addMoreBtn").click(function () {
      var html = '<tr class="addMore"><td width="30%"><input class="form-control datepicker2" placeholder="Date" type="text" name="payment_date[]" required=""></td><td width="30%"><input class="form-control online_amount" placeholder="Onile/PhonePe" type="number" name="online_amount[]" required=""><input type="hidden" class="online_amount_hidden" name="online_amount_hidden[]" value="0"></td><td width="30%"><input class="form-control cash_amount" placeholder="Offline/Cash" type="number" name="cash_amount[]" required=""><input type="hidden" class="cash_amount_hidden" name="cash_amount_hidden[]" value="0"></td><td width="10%"><a href="javascript:void(0);" class="btn btn-danger btn-sm remove" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-minus" aria-hidden="true"></i></a></td></tr>';
        //$('input').rules('add', 'required');
        $(".addMore").last().after(html);

        $('.datepicker2').datepicker({
            inline: true,
            dateFormat: "dd/mm/yy",
        });
    });

    $(document).on('click', '.remove', function(e){
      e.preventDefault();
      $(this).parent().parent().remove();
      //total();
    });
});
</script>
@endsection
