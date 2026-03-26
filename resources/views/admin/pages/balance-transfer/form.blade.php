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
              <!-- /.card-header -->
              <form id="dataForm" method="post" action="{{ route('balance-transfer.store') }}" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="purpose" value="3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Entry Date </label>
                        <input type="text" class="form-control datepicker3" placeholder="Entry Date" name="entry_date" value="{{ old('entry_date') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type" id="type">
                          <option value="">Please Select</option>
                            <option value="3">Cash to Online</option>
                            <option value="4">Online to Cash</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4" style="display: none;">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Inventory Amount</label>
                        <input type="number" class="form-control" placeholder="Inventory Amount" name="inventory_amount" id="inventory_amount" value="{{ old('inventory_amount', isset($details->inventory_amount) && $details->inventory_amount ? $details->inventory_amount : '0') }}" required="" disabled="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Online Amount</label>
                        <input type="number" class="form-control" placeholder="Online Amount" name="online_amount" value="{{ old('online_amount', isset($details->closing_online_amount) && $details->closing_online_amount ? $details->closing_online_amount : '') }}" required="" disabled="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Cash Amount</label>
                        <input type="number" class="form-control" placeholder="Cash Amount" name="cash_amount" value="{{ old('cash_amount', isset($details->closing_cash_amount) && $details->closing_cash_amount ? $details->closing_cash_amount : '') }}" required="" disabled="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" placeholder="Amount" name="amount" value="{{ old('amount', isset($details->amount) && $details->amount ? $details->amount : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Note</label>
                        <input type="text" class="form-control" placeholder="Note" name="notes" value="{{ old('notes', isset($details->notes) && $details->notes ? $details->notes : '') }}">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" id="btnSubmit">Submit</button>
                  <a href="{{ route('balance-sheet.index') }}" class="btn btn-danger">Back</a>
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
          entry_date: {
            required: true,
          },
          type: {
            required: true,
          },
          amount: {
            required: true,
            number: true,
            min: 0,
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
});
</script>
@endsection
