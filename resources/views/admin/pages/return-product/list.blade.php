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
                    <div class="col-3">
                      <div class="form-group">
                        <input type="text" class="form-control datepicker3" placeholder="Start Date" name="start_date" value="{{ isset($serach_data['start_date']) && $serach_data['start_date'] ? $serach_data['start_date'] : '' }}">
                      </div>
                    </div>
                    <div class="col-3">
                      <div class="form-group">
                        <input type="text" class="form-control datepicker3" placeholder="End Date" name="end_date" value="{{ isset($serach_data['end_date']) && $serach_data['end_date'] ? $serach_data['end_date'] : '' }}">
                      </div>
                    </div>
                    <div class="col-3">
                      <div class="form-group">
                        <select class="form-control select2" name="product_id" id="product_id">
                          <option value="">Select Product</option>
                          @if(isset($product)) && !$product->isEmpty())
                            @foreach ( $product as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($serach_data['product_id']) && $serach_data['product_id'] == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
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
              <div class="card-header">
                <h3 class="card-title">{{ $metadata['page_title'] }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Product Name</th>
                      <th>Quantity</th>
                      <th>Unit Price</th>
                      <th>Sub Total</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($rows) && !$rows->isEmpty())
                    @foreach ( $rows as $key => $res )
                    <tr> 
                      <td>{{ date('d/m/Y', strtotime($res->return_date)) }}</td>
                      <td>{{ $res->product->name }}</td>
                      <td>{{ $res->product_qty }}</td>
                      <td>{{ $res->product_unit_price }}</td>
                      <td>{{ $res->sub_total }}</td>
                      <td>{{ $res->is_active == 1 ? 'Active' : 'In-Active' }}</td>
                      <td style="width: 100px;">
                        @can('return_product_edit')
                          <a href="{{ route('return-entry.show',$res->return_entry_id) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="View">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                          </a>
                        @endcan
                      </td>
                    </tr>
                    @endforeach
                  @else
                    <tr> 
                      <td colspan="7">No record found.</td>
                    </tr>
                  @endif
                  <tr> 
                      <td colspan="2">Total Qty</td>
                      <td>{{ $product_qty }}</td>
                      <td>Total Amount</td>
                      <td colspan="3">{{ $sub_total }}</td>
                    </tr>
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
});
</script>
@endsection
