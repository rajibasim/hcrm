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
                    <div class="col-sm-5">
                      <!-- select -->
                      <div class="form-group">
                        <select class="form-control select2" name="park_id" id="park_id">
                          <option value="">Select Park</option>
                          @if($park) && !$park->isEmpty())
                            @foreach ( $park as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($serach_data['park_id']) && $serach_data['park_id'] == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <!-- select -->
                      <div class="form-group">
                        <select class="form-control select2" name="service_id" id="service_id">
                          <option value="">Select Service</option>
                          @if($service) && !$service->isEmpty())
                            @foreach ( $service as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($serach_data['service_id']) && $serach_data['service_id'] == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
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
              @can('park_service-create')
                <div class="card-header">
                  <h3 class="card-title">{{ $metadata['page_title'] }}</h3>
                    <div class="float-right">
                        <a href="{{ route('park_service.create') }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="New Records">
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
                      <th>Park Name</th>
                      <th>Service</th>
                      <th>Price</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($rows) && !$rows->isEmpty())
                    @foreach ( $rows as $key => $res )
                    <tr> 
                      <td>{{ $res->park->name }}</td>
                      <td>{{ $res->service->name }}</td>
                      <td>{{ $res->price }}</td>
                      <td>{{ $res->is_active == 1 ? 'Active' : 'In-Active' }}</td>
                      <td style="width: 100px;">
                        @can('park_service-edit')
                          <a href="{{ route('park_service.edit',$res->id) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Edit">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                          </a>
                        @endcan
                        @can('park_service-delete')
                          <form id="deleteForm{{ $res->id }}" method="POST" action="{{ route('park_service.destroy', $res->id) }}" accept-charset="UTF-8" style="display:inline">
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
