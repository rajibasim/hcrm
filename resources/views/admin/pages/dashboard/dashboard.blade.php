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
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">{{ $metadata['page_title'] }}</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row">
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3>{{ $product }}</h3>
                    <p>Product</p>
                  </div>
                  <!-- <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div> -->
                </div>
              </div>
              <!-- ./col -->
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3>{{ $product }}</h3>
                    <p>Customer</p>
                  </div>
                  <!-- <div class="icon">
                    <i class="ion ion-ios-people"></i>
                  </div> -->
                </div>
              </div>
              <!-- ./col -->
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box bg-secondary">
                  <div class="inner">
                    <h3>{{ $qty }}</h3>
                    <p>Return Qty</p>
                  </div>
                  <!-- <div class="icon">
                    <i class="ion ion-ios-people"></i>
                  </div> -->
                </div>
              </div>
              <!-- ./col -->
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3>{{ $amount }}</h3>
                    <p>Amount</p>
                  </div>
                  <!-- <div class="icon">
                    <i class="ion ion-ios-people"></i>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
@section('javascripts')

@endsection
