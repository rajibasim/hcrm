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
                        <input type="text" class="form-control datepicker3" placeholder="Invoice Date" name="invoice_date" value="{{ old('invoice_date', isset($details->invoice_date) && $details->invoice_date ? $details->invoice_date : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Delivery Date </label>
                        <input type="text" class="form-control datepicker3" placeholder="Delivery Date" name="delivery_status_update_date" value="{{ old('delivery_status_update_date', isset($details->delivery_status_update_date) && $details->delivery_status_update_date ? $details->delivery_status_update_date : '') }}" required="">
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
                        <input type="number" class="form-control billed_amount" placeholder="Billed Amount" name="billed_amount" value="{{ old('billed_amount', isset($details->billed_amount) && $details->billed_amount ? $details->billed_amount : '0') }}" required="">
                        <input type="hidden" class="billed_amount_hidden" name="billed_amount_hidden" value="{{ isset($details->billed_amount) && $details->billed_amount ? $details->billed_amount : '0' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Return Amount</label>
                        <input type="number" class="form-control return_amount" placeholder="Return Amount" name="return_amount" value="{{ old('return_amount', isset($details->return_amount) && $details->return_amount ? $details->return_amount : '0') }}" required="">
                        <input type="hidden" class="return_amount_hidden" name="return_amount_hidden" value="{{ isset($details->return_amount) && $details->return_amount ? $details->return_amount : '0' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Damage Amount</label>
                        <input type="number" class="form-control damage_amount" placeholder="Damage Amount" name="damage_amount" value="{{ old('damage_amount', isset($details->damage_amount) && $details->damage_amount ? $details->damage_amount : '0') }}" required="">
                        <input type="hidden" class="damage_amount_hidden" name="damage_amount_hidden" value="{{ isset($details->damage_amount) && $details->damage_amount ? $details->damage_amount : '0' }}">
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
                        <input type="number" class="form-control adjusment_amount" placeholder="Adjusment Amount" name="adjusment_amount" value="{{ old('adjusment_amount', isset($details->adjusment_amount) && $details->adjusment_amount ? $details->adjusment_amount : '0') }}" required="">
                        <input type="hidden" class="adjusment_amount_hidden" name="adjusment_amount_hidden" value="{{ isset($details->adjusment_amount) && $details->adjusment_amount ? $details->adjusment_amount : '0' }}">
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
                        <input type="text" class="form-control" placeholder="Note" name="note" value="{{ old('note', isset($details->note) && $details->note ? $details->note : '') }}">
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
                              <td width="20%">Onile/PhonePe</td>
                              <td width="20%">Offline/Cash</td>
                              <td width="20%">Attachment</td>
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
                                  <input class="form-control datepicker3" placeholder="Date" type="text" name="payment_date[]">
                                </td>
                                <td width="20%">
                                  <input class="form-control online_amount" placeholder="Onlie/PhonePe" type="number" name="online_amount[]" value="0">
                                  <input type="hidden" class="online_amount_hidden" name="online_amount_hidden[]" value="0">
                                </td>
                                <td width="20%">
                                  <input class="form-control cash_amount" placeholder="Offline/Cash" type="number" name="cash_amount[]" value="0">
                                  <input type="hidden" class="cash_amount_hidden" name="cash_amount_hidden[]" value="0">
                                </td>
                                <td width="20%">
                                  <input class="form-control attachment" type="file" name="attachment[]">
                                  <input type="hidden" class="attachment_hidden" name="attachment_hidden[]" value="0">
                                </td>
                                <td width="10%">
                                  <a href="javascript:void(0);" class="btn btn-primary btn-sm addMoreBtn" data-toggle="tooltip" data-placement="top" title="Add New">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  </a>
                                </td>
                              </tr>
                            @endif
                            <tr>
                               <td colspan="4">Balance Amount</td>
                               <td colspan="1" id="balance_amount">{{ isset($details->balance_amount) && $details->balance_amount ? $details->balance_amount : '0.00' }}</td>
                               <input type="hidden" class="balance_amount" name="balance_amount" value="{{ isset($details->balance_amount) && $details->balance_amount ? $details->balance_amount : '0' }}">
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
            bill_number: { required: true },
            invoice_date: { required: true},
            delivery_status_update_date: { required: true},
            delivery_status_id: { required: true },
            sales_person_id: { required: true },
            customer_id: { required: true },
            billed_amount: { required: true, number: true, min: 0 },
            return_amount: { required: true, number: true, min: 0 },
            damage_amount: { required: true, number: true, min: 0 },
            adjusment_percent: { required: true, number: true, min: 0 },
            adjusment_amount: { required: true, number: true, min: 0 },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });

    $(".addMoreBtn").click(function () {
        var html = `
        <tr class="addMore">
            <td width="30%"><input class="form-control datepicker3" placeholder="Date" type="text" name="payment_date[]"></td>
            <td width="20%"><input class="form-control online_amount" placeholder="Online/PhonePe" type="number" name="online_amount[]" value="0"><input type="hidden" class="online_amount_hidden" name="online_amount_hidden[]" value="0"></td>
            <td width="20%"><input class="form-control cash_amount" placeholder="Offline/Cash" type="number" name="cash_amount[]" value="0"><input type="hidden" class="cash_amount_hidden" name="cash_amount_hidden[]" value="0"></td>
            <td width="20%"><input class="form-control attachment" type="file" name="attachment[]"><input type="hidden" class="attachment_hidden" name="attachment_hidden[]" value="0"></td>
            <td width="10%"><a href="javascript:void(0);" class="btn btn-danger btn-sm remove"><i class="fa fa-minus"></i></a></td>
        </tr>`;

        $(".addMore").last().after(html);

        $('.datepicker3').datepicker({
            inline: true,
            dateFormat: "yy-mm-dd",
        });
        balance_amount();
    });

    $(document).on("click", ".remove", function () {
        $(this).closest("tr").remove();
        balance_amount();
    });

    $(document).on('change', 'input.attachment', function() {
        var $row = $(this).closest('tr'); // get the row
        var $hiddenInput = $row.find('.attachment_hidden'); // find hidden input in the same row
        $hiddenInput.val('1'); // set value
    });

    $(document).on('keyup', '.billed_amount', function(e){
        var billed_amount = $(this).val();
        if(billed_amount.length > 0 && billed_amount > 0){
          $('.billed_amount_hidden').val(billed_amount);
        }else{
          $('.billed_amount_hidden').val(0);
        }

        balance_amount();
    });

    $(document).on('keyup', '.return_amount', function(e){
        var return_amount = $(this).val();
        if(return_amount.length > 0 && return_amount > 0){
          $('.return_amount_hidden').val(return_amount);
        }else{
          $('.return_amount_hidden').val(0);
        }

        balance_amount();
    });

    $(document).on('keyup', '.damage_amount', function(e){
        var damage_amount = $(this).val();
        if(damage_amount.length > 0 && damage_amount > 0){
          $('.damage_amount_hidden').val(damage_amount);
        }else{
          $('.damage_amount_hidden').val(0);
        }

        balance_amount();
    });

    $(document).on('keyup', '.adjusment_amount', function(e){
        var adjusment_amount = $(this).val();
        if(adjusment_amount.length > 0 && adjusment_amount > 0){
          $('.adjusment_amount_hidden').val(adjusment_amount);
        }else{
          $('.adjusment_amount_hidden').val(0);
        }

        balance_amount();
    });

    $(document).on('keyup', '.online_amount', function(){
        var online_amount = $(this).val();
        if(online_amount.length > 0 && online_amount > 0){
          $(this).next('input').val(online_amount);
        }else{
          $(this).next('input').val(0);
        }

        balance_amount();
    });

    $(document).on('keyup', '.cash_amount', function(){
        var cash_amount = $(this).val();
        if(cash_amount.length > 0 && cash_amount > 0){
          $(this).next('input').val(cash_amount);
        }else{
          $(this).next('input').val(0);
        }
        balance_amount();
    });

    function balance_amount(){
        var online_total = 0;
        var online = $(".online_amount_hidden");
        for(var i = 0; i < online.length; i++){
            online_total = online_total + parseFloat($(online[i]).val());
        }

        var offline_total = 0;
        var offline = $(".cash_amount_hidden");
        for(var i = 0; i < offline.length; i++){
            offline_total = offline_total + parseFloat($(offline[i]).val());
        }

        var billed_amount_hidden = parseFloat($('.billed_amount_hidden').val());
        var return_amount_hidden = parseFloat($('.return_amount_hidden').val());
        var damage_amount_hidden = parseFloat($('.damage_amount_hidden').val());
        var adjusment_amount_hidden = parseFloat($('.adjusment_amount_hidden').val());

        //console.log(billed_amount_hidden);

        var total = billed_amount_hidden - (online_total + offline_total + return_amount_hidden + damage_amount_hidden + adjusment_amount_hidden);

        $("#balance_amount").html(total.toFixed(2));
        $(".balance_amount").val(total);
    }
});
</script>
@endsection
