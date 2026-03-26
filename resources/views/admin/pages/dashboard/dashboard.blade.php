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
    @if(intval(auth()->user()->sales_person_id) > 0)
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row">
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box" style="background-color: #43E617 !important; color: #fff;">
                  <div class="inner">
                    <h3>{{ $billData->total_number_of_bill }}</h3>
                    <p>Total No Bill</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box bg-dark">
                  <div class="inner">
                    <h3>{{ $billData->billed_amount }}</h3>
                    <p>Total Billed Amount</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box" style="background-color: #F5F018 !important; color: #fff;">
                  <div class="inner">
                    <h3>{{ number_format($billData->damage_amount + $billData->return_amount + $billData->adjusment_amount, 2) }}</h3>
                    <p>Total Adjust Amount</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3>{{ number_format($billData->balance_amount, 2) }}</h3>
                    <p>Amount Due</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                </div>
              </div>
              <!-- <div class="col-md-3 col-sm-12">
                <div class="small-box" style="background-color: #E68917 !important; color: #fff">
                  <div class="inner">
                    <h3>{{ number_format($billData->damage_amount, 2) }}</h3>
                    <p>Damage Amount</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                  <a href="{{ route('credit-history.index') }}?damage_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="small-box" style="background-color: #8A2020 !important; color: #fff">
                  <div class="inner">
                    <h3>{{ number_format($billData->return_amount, 2) }}</h3>
                    <p>Return Amount</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                  <a href="{{ route('credit-history.index') }}?return_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="small-box" style="background-color: #910633 !important; color: #fff">
                  <div class="inner">
                    <h3>{{ number_format($billData->adjusment_amount, 2) }}</h3>
                    <p>Adjusment Amount</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                  </div>
                  <a href="{{ route('credit-history.index') }}?adjusment_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div> -->
            </div>
          </div>
      </div><!-- /.container-fluid -->
    </section>
    @else
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="row">
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box btn-primary">
                    <div class="inner">
                      <h3>{{ $customer }}</h3>
                      <p>Customer</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('customer.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3>{{ isset($txnDetails->closing_inventory_amount) && $txnDetails->closing_inventory_amount ? $txnDetails->closing_inventory_amount : '0.00' }}</h3>
                      <p>Inventory</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('inventory-history.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box bg-secondary">
                    <div class="inner">
                      <h3>{{ isset($txnDetails->closing_online_amount) && $txnDetails->closing_online_amount ? $txnDetails->closing_online_amount : '0.00' }}</h3>
                      <p>Online Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('payment-history.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3>{{ isset($txnDetails->closing_cash_amount) && $txnDetails->closing_cash_amount ? $txnDetails->closing_cash_amount : '0.00' }}</h3>
                      <p>Cash Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('payment-history.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box" style="background-color: #43E617 !important; color: #fff;">
                    <div class="inner">
                      <h3>{{ $billData->total_number_of_bill }}</h3>
                      <p>Total No Bill</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('bill.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box bg-dark">
                    <div class="inner">
                      <h3>{{ $billData->billed_amount }}</h3>
                      <p>Total Billed Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('bill.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box" style="background-color: #F5F018 !important; color: #fff;">
                    <div class="inner">
                      <h3>{{ number_format($billData->damage_amount + $billData->return_amount + $billData->adjusment_amount, 2) }}</h3>
                      <p>Total Adjust Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('credit-report.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>{{ number_format($billData->balance_amount, 2) }}</h3>
                      <p>Amount Due</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('credit-history.index') }}?balance_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box" style="background-color: #d01f1f !important; color: #fff">
                    <div class="inner">
                      <h3>{{ $paymentRequest }}</h3>
                      <p>Payment Request</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('bil-payment-history.index') }}?wt_aprove=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box" style="background-color: #E68917 !important; color: #fff">
                    <div class="inner">
                      <h3>{{ number_format($billData->damage_amount, 2) }}</h3>
                      <p>Damage Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('credit-history.index') }}?damage_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box" style="background-color: #8A2020 !important; color: #fff">
                    <div class="inner">
                      <h3>{{ number_format($billData->return_amount, 2) }}</h3>
                      <p>Return Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('credit-history.index') }}?return_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12">
                  <!-- small box -->
                  <div class="small-box" style="background-color: #910633 !important; color: #fff">
                    <div class="inner">
                      <h3>{{ number_format($billData->adjusment_amount, 2) }}</h3>
                      <p>Adjusment Amount</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('credit-history.index') }}?adjusment_amount=1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
        </div><!-- /.container-fluid -->
      </section>
    @endif
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
@section('javascripts')

@endsection
