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
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('balance-sheet.update', $details->id) : route('balance-sheet.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Entry Date </label>
                        <input type="text" class="form-control datepicker3" placeholder="Entry Date" name="entry_date" value="{{ old('entry_date', isset($details->entry_date) && $details->entry_date ? $details->entry_date : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" placeholder="Amount" name="amount" value="{{ old('amount', isset($details->amount) && $details->amount ? $details->amount : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- select -->
                      <div class="form-group">
                        <label>Type</label>
                        <select class="form-control select2" name="type" id="type">
                          <option value="">Please Select</option>
                          <option value="1" {{ isset($details->type) && $details->type == 1 ? 'selected' : '' }}>Credit</option>
                          <option value="2" {{ isset($details->type) && $details->type == 2 ? 'selected' : '' }}>Debit</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-3">
                      <!-- select -->
                      <div class="form-group">
                        <label>Purpose</label>
                        <select class="form-control" name="purpose" id="purpose">
                          <option value="">Please Select</option>
                          <span id="purpose1">
                            <option value="1" {{ isset($details->purpose) && $details->purpose == 1 ? 'selected' : '' }}>Cash</option>
                            <option value="2" {{ isset($details->purpose) && $details->purpose == 2 ? 'selected' : '' }}>Online/Phone-Pe</option>
                            <option value="3" {{ isset($details->purpose) && $details->purpose == 3 ? 'selected' : '' }}>Stock</option>
                            <option value="4" {{ isset($details->purpose) && $details->purpose == 4 ? 'selected' : '' }}>Other Stock</option>
                            <option value="5" {{ isset($details->purpose) && $details->purpose == 5 ? 'selected' : '' }}>Damage</option>
                          </span>
                          <span id="purpose2">
                            <option value="6" {{ isset($details->purpose) && $details->purpose == 6 ? 'selected' : '' }}>Invoice</option>
                            <option value="7" {{ isset($details->purpose) && $details->purpose == 7 ? 'selected' : '' }}>Daliy Expenses</option>
                            <option value="8" {{ isset($details->purpose) && $details->purpose == 8 ? 'selected' : '' }}>Salary</option>
                            <option value="9" {{ isset($details->purpose) && $details->purpose == 9 ? 'selected' : '' }}>Rent</option>
                            <option value="10" {{ isset($details->purpose) && $details->purpose == 10 ? 'selected' : '' }}>Oil</option>
                            <option value="11" {{ isset($details->purpose) && $details->purpose == 11 ? 'selected' : '' }}>Other</option>
                          </span>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-12">
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
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ route('balance-sheet.index') }}" class="btn btn-danger">Reset</a>
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
          purpose: {
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

    var typeVal = $("#type").val();
    if (typeVal === "1") {
          // Enable options 1–5
          $("#purpose1").show();
          $("#purpose2").hide();
    }else if (typeVal === "2") {
          // Enable options 6–11
          $("#purpose2").show();
          $("#purpose1").hide();
    }

    $("#type").change(function() {
      var typeVal = $(this).val();
      $("#purpose").val("").trigger("change"); // clear selected value
      if (typeVal === "1") {
            // Enable options 1–5
            $("#purpose1").show();
            $("#purpose2").hide();
      }else if (typeVal === "2") {
            // Enable options 6–11
            $("#purpose2").show();
            $("#purpose1").hide();
      }
    });
});
</script>
@endsection
