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
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('parks.update', $details->id) : route('parks.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Logo</label>
                        <input type="file" class="form-control" placeholder="Logo" name="banner" value="" accept="image/png, image/gif, image/jpeg">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name', isset($details->name) && $details->name ? $details->name : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" placeholder="Address" name="address" value="{{ old('address', isset($details->address) && $details->address ? $details->address : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" placeholder="Country" name="country" value="{{ old('country', isset($details->country) && $details->country ? $details->country : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>State</label>
                        <input type="text" class="form-control" placeholder="State" name="state" value="{{ old('state', isset($details->state) && $details->state ? $details->state : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" placeholder="City" name="city" value="{{ old('city', isset($details->city) && $details->city ? $details->city : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Pin Code</label>
                        <input type="text" class="form-control" placeholder="Pin Code" name="zip_code" value="{{ old('zip_code', isset($details->zip_code) && $details->zip_code ? $details->zip_code : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" class="form-control" placeholder="Latitude" name="latitude" value="{{ old('latitude', isset($details->latitude) && $details->latitude ? $details->latitude : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" class="form-control" placeholder="Longitude" name="longitude" value="{{ old('longitude', isset($details->longitude) && $details->longitude ? $details->longitude : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Contact Email</label>
                        <input type="text" class="form-control" placeholder="Email" name="contact_email" value="{{ old('contact_email', isset($details->contact_email) && $details->contact_email ? $details->contact_email : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" class="form-control" placeholder="Email" name="contact_phone" value="{{ old('contact_phone', isset($details->contact_phone) && $details->contact_phone ? $details->contact_phone : '') }}" required="">
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
                        <label>Short Description</label>
                        <input type="text" class="form-control" placeholder="Short Description" name="short_description" value="{{ old('short_description', isset($details->short_description) && $details->short_description ? $details->short_description : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" required="" name="description">{{ old('description', isset($details->description) && $details->description ? $details->description : '') }}</textarea>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" class="form-control" placeholder="Meta Title" name="meta_title" value="{{ old('meta_title', isset($details->meta_title) && $details->meta_title ? $details->meta_title : '') }}">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Meta Description</label>
                        <input type="text" class="form-control" placeholder="Meta Description" name="meta_description" value="{{ old('meta_description', isset($details->meta_description) && $details->meta_description ? $details->meta_description : '') }}">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ route('parks.index') }}" class="btn btn-danger">Reset</a>
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
          name: {
            required: true,
          },
          address: {
            required: true,
          },
          country: {
            required: true,
          },
          state: {
            required: true,
          }, 
          city: {
            required: true,
          },
          zip_code: {
            required: true,
            number: true,
          },
          latitude: {
            required: true,
          },
          longitude: {
            required: true,
          },
          short_description: {
            required: true,
          },
          description: {
            required: true,
          },
          contact_email: {
            required: true,
            email: true
          },
          contact_phone: {
            required: true,
            number: true,
          },
          banner: {
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
});
</script>
@endsection
