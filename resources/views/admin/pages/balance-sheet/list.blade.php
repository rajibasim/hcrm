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
                      <select class="form-control select2" name="purpose" id="purpose">
                        <option value="">Please select</option>
                        <option value="1" {{ isset($serach_data['purpose']) && $serach_data['purpose'] == 1 ? 'selected' : '' }}>Invest</option>
                        <option value="2" {{ isset($serach_data['purpose']) && $serach_data['purpose'] == 2 ? 'selected' : '' }}>Withdraw</option>
                      </select>
                    </div>
                    <div class="col-sm-3">
                      <!-- select -->
                      <div class="form-group">
                        <input type="text" class="form-control datepicker3" placeholder="Start Date" name="start_date" value="{{ isset($serach_data['start_date']) && $serach_data['start_date'] ? $serach_data['start_date'] : '' }}" >
                      </div>
                    </div>
                    <div class="col-sm-3">
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
              @can('balance_sheet_create')
                <div class="card-header">
                  <h3 class="card-title">{{ $metadata['page_title'] }}</h3>
                    <div class="float-right">
                        <a href="{{ route('balance-sheet.create') }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="New Records">
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
                      <th>Date</th>
                      <th>Purpose</th>
                      <th>Inventory</th>
                      <th>Online</th>
                      <th>Cash</th>
                      <th>Note</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($rows) && !$rows->isEmpty())
                    @foreach ( $rows as $key => $res )
                      <tr> 
                        <td rowspan="3">{{ $res->entry_date }}</td>
                        <td rowspan="3">{{ $res->purpose == 4 ? 'Invest' : 'Withdraw' }}</td>
                        <td>{{ $res->inventory_amount }}</td>
                        <td>{{ $res->online_amount }}</td>
                        <td>{{ $res->cash_amount }}</td>
                        <td rowspan="3"><p>{{ $res->notes }}</p></td>
                        <td style="width: 100px;" rowspan="3">
                            @can('balance_sheet_edit')
                              <a href="{{ route('balance-sheet.edit',$res->id) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="View">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                              </a>
                            @endcan
                        </td>
                      </tr>
                      <tr> 
                        <td>{{ $res->opening_inventory_amount }}</td>
                        <td>{{ $res->opening_online_amount }}</td>
                        <td>{{ $res->opening_cash_amount }}</td>
                      </tr>
                      <tr> 
                        <td>{{ $res->closing_inventory_amount }}</td>
                        <td>{{ $res->closing_online_amount }}</td>
                        <td>{{ $res->closing_cash_amount }}</td>
                      </tr>
                    @endforeach
                  @else
                    <tr> 
                      <td colspan="8">No record found.</td>
                    </tr>
                  @endif
                  </tbody>
                  <!-- <thead>
                    <tr>
                      <th colspan="2">Total Invest</th>
                      <th>{{ $invest_inventory_amount }}</th>
                      <th>{{ $invest_online_amount }}</th>
                      <th>{{ $invest_cash_amount }}</th>
                      <th colspan="3">{{ $invest_inventory_amount + $invest_online_amount + $invest_cash_amount }}</th>
                    </tr>
                  </thead>
                  <thead>
                    <tr>
                      <th colspan="2">Total withdaraw</th>
                      <th>{{ $withdaraw_inventory_amount }}</th>
                      <th>{{ $withdaraw_online_amount }}</th>
                      <th>{{ $withdaraw_cash_amount }}</th>
                      <th colspan="3">{{ $withdaraw_inventory_amount + $withdaraw_online_amount + $withdaraw_cash_amount }}</th>
                    </tr>
                  </thead> -->
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
