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
          <div class="col-12">
            <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Note:</h5>
              {{ $ticket->park->short_description }}
            </div>


            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <img src="{{ asset('storage/banner/'.$ticket->park->banner) }}" alt="parkLogo" width="50px" height="50px"> {{ $ticket->park->name }}
                    <small class="float-right">Date: {{ date('d/m/Y', strtotime($ticket->booking_date)) }}</small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  From
                  <address>
                    <strong>{{ $ticket->park->name }}</strong><br>
                    {{ $ticket->park->address }}<br>
                    {{ $ticket->park->zip_code }}<br>
                    Phone: {{ $ticket->park->contact_phone }}<br>
                    Email: {{ $ticket->park->contact_email }}
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  To
                  <address>
                    <strong>{{ $ticket->customer->name }}</strong><br>
                    {{ $ticket->customer->address }}<br>
                    Phone: {{ $ticket->customer->phone }}<br>
                    <!-- Email: john.doe@example.com -->
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>Ticket No : {{ $ticket->ticket_no }}</b><br>
                  <br>
                  <b>Amount Paid :</b> {{ date('d/m/Y', strtotime($ticket->booking_date)) }}<br>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- Table row -->
              <div class="row">
                <!-- /.col -->
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th colspan="4" style="text-align: center;">Entry</th>
                    </tr>
                    </thead>
                    <tbody>
                      @if(isset($parkEntry) && count($parkEntry) > 0)
                        @foreach ( $parkEntry as $key => $res )
                          <tr>
                            <td>{{ $res->name }}</td>
                            <td>{{ $res->unit_price }}</td>
                            <td>{{ $res->quantity }}</td>
                            <td>{{ $res->sub_total }}</td>
                          </tr>
                        @endforeach
                          <tr> 
                            <td colspan="3">entry Total</td>
                            <td id="service_total">{{ $ticket->entry_sub_total > 0 ?  $ticket->entry_sub_total : '0.00' }}</td>
                          </tr>
                      @else
                        <tr> 
                          <td colspan="4">No entry avalible.</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th colspan="4" style="text-align: center;">Services</th>
                    </tr>
                    </thead>
                    <tbody>
                      @if(isset($parkService) && count($parkService) > 0)
                        @foreach ( $parkService as $key => $res )
                          <tr>
                            <td>{{ $res->name }}</td>
                            <td>{{ $res->unit_price }}</td>
                            <td>{{ $res->quantity }}</td>
                            <td>{{ $res->sub_total }}</td>
                          </tr>
                        @endforeach
                          <tr> 
                            <td colspan="3">Service Total</td>
                            <td id="service_total">{{ $ticket->service_sub_total > 0 ?  $ticket->service_sub_total : '0.00' }}</td>
                          </tr>
                      @else
                        <tr> 
                          <td colspan="4">No service avalible.</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <!-- /.col -->
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th colspan="4" style="text-align: center;">Activity</th>
                    </tr>
                    </thead>
                    <tbody>
                      @if(isset($parkActivity) && count($parkActivity) > 0)
                        @foreach ( $parkActivity as $key => $res )
                          <tr>
                            <td>{{ $res->name }}</td>
                            <td>{{ $res->unit_price }}</td>
                            <td>{{ $res->quantity }}</td>
                            <td>{{ $res->sub_total }}</td>
                          </tr>
                        @endforeach
                          <tr> 
                            <td colspan="3">Activity Total</td>
                            <td id="service_total">{{ $ticket->activity_sub_total > 0 ?  $ticket->activity_sub_total : '0.00' }}</td>
                          </tr>
                      @else
                        <tr> 
                          <td colspan="4">No activity avalible.</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead">Ticket QR Code:</p>
                  @php
                    $ticket_no = $ticket->ticket_no;
                    $string = base64_encode($ticket_no);
                    $google_chart_api_url = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=".$string."&choe=UTF-8";
                  @endphp
                  <img src="{{ $google_chart_api_url }}" alt="{{ $string }}" style="height: 100px; width: 100px;">
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th>Total:</th>
                        <td>{{ $ticket->total }}</td>
                      </tr>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-12">
                  <a href="print/{{ $ticket->id }}" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                  <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
                    Payment
                  </button>
                  <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generate PDF
                  </button>
                </div>
              </div>
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
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
