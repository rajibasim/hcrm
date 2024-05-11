<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticket Print</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('admin-assets/plugins/plugins/fontawesome-free/css/all.min.css?version='.config('app.version')) }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('admin-assets/css/adminlte.min.css?version='.config('app.version')) }}">
</head>
<body>
<div class="wrapper">
  <!-- Main content -->
  <div class="invoice">
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
        <a href="#" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
        <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
          Payment
        </button>
        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
          <i class="fas fa-download"></i> Generate PDF
        </button>
      </div>
    </div>
  </div>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
<!-- Page specific script -->
<script>
  window.addEventListener("load", window.print());
</script>
</body>
</html>
