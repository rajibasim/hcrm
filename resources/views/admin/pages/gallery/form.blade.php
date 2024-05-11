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
              <form id="dataForm" method="post" action="{{ isset($details->id) && $details->id ? route('gallery.update', $details->id) : route('gallery.store') }}" autocomplete="off" enctype="multipart/form-data">
                @if(isset($details->id) && $details->id)
                  <input name="_method" type="hidden" value="PATCH">
                @endif
                @csrf
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-4">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" placeholder="Title" name="title" value="{{ old('title', isset($details->title) && $details->title ? $details->title : '') }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Type</label>
                        <select class="form-control select2" name="type" id="type">
                          <option value="1" {{ isset($details->type) && $details->type == 1 ? 'selected' : '' }}>Image</option>
                          <option value="2" {{ isset($details->type) && $details->type == 2 ? 'selected' : '' }}>Video</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-4" id="sec_image" style="display: {{ isset($details->type) && ($details->type == 1) ? '' : 'none' }};">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" placeholder="Banner Image" name="gallery_image" value="" accept="image/png, image/gif, image/jpeg">
                      </div>
                    </div>
                    <div class="col-sm-4" id="sec_video" style="display: {{ isset($details->type) && ($details->type == 2) ? '' : 'none' }};">
                      <!-- text input -->
                      <div class="form-group">
                        <label>Video</label>
                        <input type="text" class="form-control" placeholder="Video URL" name="video_url" value="{{ isset($details->media) && $details->media ? $details->media : '' }}" required="">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <!-- select -->
                      <div class="form-group">
                        <label>Park</label>
                        <select class="form-control select2" name="park_id" id="park_id" required="">
                          <option value="">Select Park</option>
                          @if($park) && !$park->isEmpty())
                            @foreach ( $park as $key => $res )
                              <option value="{{ $res->id }}" {{ isset($details->park_id) && $details->park_id == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                          @endif
                        </select>
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
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ route('gallery.index') }}" class="btn btn-danger">Reset</a>
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
          title: {
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

    $("#type").change(function(){
        var type = $(this).val();         
        if(type == 1){
            $("#sec_image").show();
            $("#sec_video").hide();
        }else{
            $("#sec_video").show();
            $("#sec_image").hide();
        }
    });
});
</script>
@endsection
