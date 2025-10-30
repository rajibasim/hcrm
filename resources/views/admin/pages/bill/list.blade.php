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
                            <a href="{{ $breadcumb['url'] }}">{{ $breadcumb['title'] }}</a>
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
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Search</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              @php
                $serach_data = isset($metadata['serach_data']) && $metadata['serach_data'] ? $metadata['serach_data'] : '';
              @endphp
              <div class="card-body">
                <form method="get" action="" autocomplete="off" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="Bill No" name="bill_number" value="{{ isset($serach_data['bill_number']) && $serach_data['bill_number'] ? $serach_data['bill_number'] : '' }}">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <select class="form-control select2" name="sales_person_id" id="sales_person_id" >
                          <option value="">Select Sales Person</option>
                          @if($SalesPerson) && !$SalesPerson->isEmpty())
                            @foreach ( $SalesPerson as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($serach_data['sales_person_id']) && $serach_data['sales_person_id'] == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <select class="form-control select2" name="customer_id" id="customer_id" >
                          <option value="">Select Customer</option>
                          @if($customer) && !$customer->isEmpty())
                            @foreach ( $customer as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($serach_data['customer_id']) && $serach_data['customer_id'] == $res->id ? 'selected' : '' }}>{{ $res->party_name }}-{{ $res->party_code }}-{{ $res->beat }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <input type="text" class="form-control datepicker3" placeholder="Invoice Date" name="invoice_date" value="{{ isset($serach_data['invoice_date']) && $serach_data['invoice_date'] ? $serach_data['invoice_date'] : '' }}" >
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <input type="text" class="form-control datepicker3" placeholder="Start Date" name="start_date" value="{{ isset($serach_data['start_date']) && $serach_data['start_date'] ? $serach_data['start_date'] : '' }}" >
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <input type="text" class="form-control datepicker3" placeholder="End Date" name="end_date" value="{{ isset($serach_data['end_date']) && $serach_data['end_date'] ? $serach_data['end_date'] : '' }}" >
                      </div>
                    </div>
                    <div class="col-1">
                      <button type="submit" class="btn btn-block btn-primary">Search</button>
                    </div>
                    <div class="col-1">
                      <a href="{{ url($metadata['page_url']) }}" class="btn btn-block btn-danger">Reset</a>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- ./row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-outline">
              @can('bill_entry_view')
                <div class="card-header">
                  <h3 class="card-title">{{ $metadata['page_title'] }}</h3>
                    <div class="float-right">
                        <a href="{{ route('bill.create') }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="New Records">
                          <i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
              @endcan
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Bill No</th>
                      <th>Customer</th>
                      <th>Bill Date</th>
                      <th>Delivery Status</th>
                      <th>Sales Person</th>
                      <th>Billed Amount</th>
                      <th>Paid Amount</th>
                      <th>Balance Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($rows) && !$rows->isEmpty())
                    @foreach ( $rows as $key => $res )
                    <tr> 
                      <td>{{ $res->bill_number }}</td>
                      <td>{{ $res->customer->party_name }}</td>
                      <td>{{ $res->invoice_date }}</td>
                      <td>{{ $res->DeliveryStatus->name }}</td>
                      <td>{{ $res->SalesPerson->name }}</td>
                      <td>{{ $res->billed_amount }}</td>
                      <td>{{ number_format($res->billed_amount - $res->balance_amount, 2) }}</td>
                      <td>{{ $res->balance_amount }}</td>
                      <td style="width: 100px;">
                        @can('customer_edit')
                          <a href="{{ route('bill.edit',$res->id) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Edit">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                          </a>
                        @endcan
                        @can('bill_entry_delete')
                          <form id="deleteForm{{ $res->id }}" method="POST" action="{{ route('bill.destroy', $res->id) }}" accept-charset="UTF-8" style="display:inline">
                              <input name="_method" type="hidden" value="DELETE">
                              <a id="{{ $res->id }}" href="javascript:void(0);" class="btn btn-danger btn-sm single" data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                              </a>
                            @csrf
                          </form>
                        @endcan
                      </td>
                    </tr>
                    @endforeach
                  @else
                    <tr> 
                      <td colspan="5">No record found.</td>
                    </tr>
                  @endif
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <div class="float-right">
                  @if(isset($rows) && $rows)
                    {!! $rows->appends(Request::all())->links() !!}
                  @endif
                </div>
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
    @if(Session::has('flash_data')) 
      @php 
        $flash_data = Session::pull('flash_data');
      @endphp
      toastr.{{ $flash_data['status'] }}("{{ $flash_data['message'] }}");
    @endif

    var clicked = false;
    $(".checkall").on("click", function() {
        $(".checkbox").prop("checked", !clicked);
        clicked = !clicked;
        this.innerHTML = clicked ? 'Deselect' : 'Select';
    });
    

    $(".single").on("click", function(e) {
        e.preventDefault();
        var delete_url = $(this).attr('href');
        Swal.fire({
          title: 'Are you sure you want to delete this?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            var id = $(this).attr('id');
            $('form#deleteForm'+id).submit();
          }
        })
    });
});
</script>
@endsection
