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
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('customer.update', $details->id) : route('customer.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Party Name</label>
                        <input type="text" class="form-control" placeholder="Party Name" name="party_name" value="{{ old('party_name', isset($details->party_name) && $details->party_name ? $details->party_name : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Phone No</label>
                        <input type="text" class="form-control" placeholder="Phone No" name="phone_no" value="{{ old('phone_no', isset($details->phone_no) && $details->phone_no ? $details->phone_no : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Party Code</label>
                        <input type="text" class="form-control" placeholder="Party Code" name="party_code" value="{{ old('party_code', isset($details->party_code) && $details->party_code ? $details->party_code : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" placeholder="Address" name="address" value="{{ old('address', isset($details->address) && $details->address ? $details->address : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Beat</label>
                        <input type="text" class="form-control" placeholder="Beat" name="beat" value="{{ old('beat', isset($details->beat) && $details->beat ? $details->beat : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Party Channel</label>
                        <input type="text" class="form-control" placeholder="Party Channel" name="party_channel" value="{{ old('party_channel', isset($details->party_channel) && $details->party_channel ? $details->party_channel : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Channel</label>
                        <input type="text" class="form-control" placeholder="Channel" name="channel" value="{{ old('channel', isset($details->channel) && $details->channel ? $details->channel : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>HUL Code</label>
                        <input type="text" class="form-control" placeholder="HUL Code" name="hul_code" value="{{ old('hul_code', isset($details->hul_code) && $details->hul_code ? $details->hul_code : '') }}" required="">
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
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                  <a href="{{ route('customer.index') }}" class="btn btn-danger">Back</a>
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
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Bill History</h3>
                  <div class="float-right">
                      <!-- <a href="{{ route('bill.create') }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="New Records">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                      </a> -->
                  </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Bill No</th>
                      <th>Payment Date</th>
                      <th>Billed Amount</th>
                      <th>Damage</th>
                      <th>Return</th>
                      <th>Adjusment</th>
                      <th>Online</th>
                      <th>Cash</th>
                      <th>Due</th>
                      <th>Sales Person</th>
                      <th>Created At</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($rows) && !$rows->isEmpty())
                    @foreach ( $rows as $key => $res )
                    <tr> 
                      <td><a href="{{ route('bill.show',$res->id) }}"> {{ $res->bill_number }}</a></td>
                      <td>{{ $res->invoice_date }}</td>
                      <td>{{ $res->billed_amount }}</td>
                      <td>{{ $res->damage_amount }}</td>
                      <td>{{ $res->return_amount }}</td>
                      <td>{{ $res->adjusment_amount }}</td>
                      <td>{{ $res->online_amount }}</td>
                      <td>{{ $res->cash_amount }}</td>
                      <td>{{ $res->balance_amount }}</td>
                      <td>{{ $res->SalesPerson->name }}</td>
                      <td>{{ date('Y-m-d', strtotime($res->created_at)) }}</td>
                    </tr>
                    @endforeach
                  @else
                    <tr> 
                      <td colspan="12">No record found.</td>
                    </tr>
                  @endif
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.card -->
          </div>
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
          party_name: {
            required: true,
          },
          phone_no: {
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

    $('input, select, textarea, button').prop('disabled', true);
});
</script>
@endsection
