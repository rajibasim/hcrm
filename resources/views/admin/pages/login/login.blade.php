<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} || Login</title>
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('public/admin-assets/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="{{ asset('public/admin-assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('public/admin-assets/css/adminlte.min.css') }}">
         <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('public/admin-assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css?version='.config('app.version')) }}">
        <link rel="stylesheet" href="{{ asset('public/admin-assets/plugins/toastr/toastr.min.css?version='.config('app.version')) }}">
   </head>
   </head>
   <body class="hold-transition login-page">
      <div class="login-box" id="app">
         <!-- /.login-logo -->
         <div class="card card-outline card-primary">
            <div class="card-header text-center">
               <a href="admin-login" class="h1">
                  <b>{{ env('APP_NAME') }}</b> System
               </a>
            </div>
            <div class="card-body">
               <p class="login-box-msg">Configure of Admin Panel</p>
               <form id="login-form" action="{{ route('login.post') }}" method="post">
                  @csrf
                  <div class="input-group mb-3">
                     <div class="input-group-append">
                        <div class="input-group-text">
                           <span class="fas fa-envelope"></span>
                        </div>
                     </div>
                     <input type="email" class="form-control" placeholder="E-mail address" id="email" name="email" value='{{ old('email') }}' />
                  </div>
                  <div class="input-group mb-3">
                     <div class="input-group-append">
                        <div class="input-group-text">
                           <span class="fas fa-lock"></span>
                        </div>
                     </div>
                     <input type="password" class="form-control" placeholder="Password" id="password" name="password" required />
                  </div>
                  <div class="row">
                     <div class="col-8">
                        <div class="icheck-primary">
                           <input class="form-check-input" type="checkbox" name="remember" id="remember" checked="">
                           <label for="remember">
                           Remember Me
                           </label>
                        </div>
                     </div>
                     <!-- /.col -->
                     <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            {{ __('Login') }}
                        </button>
                     </div>
                     <!-- /.col -->
                  </div>
               </form>
               <!-- /.social-auth-links -->
            </div>
            <!-- /.card-body -->
         </div>
         <!-- /.card -->
      </div>
      <!-- /.login-box -->
      <!-- jQuery -->
      <script src="{{ asset('public/admin-assets/plugins/jquery/jquery.min.js') }}"></script>
      <!-- Bootstrap 4 -->
      <script src="{{ asset('public/admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
      <!-- SweetAlert2 -->
      <script src="{{ asset('public/admin-assets/plugins/sweetalert2/sweetalert2.min.js?version='.config('app.version')) }}"></script>
      <!-- Toastr -->
      <script src="{{ asset('public/admin-assets/plugins/toastr/toastr.min.js?version='.config('app.version')) }}"></script>
      <!-- AdminLTE App -->
      <script src="{{ asset('public/admin-assets/js/adminlte.min.js') }}"></script>
      <script src="{{ asset('public/admin-assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
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

            @if(Session::has('flash_data')) 
              @php 
                $flash_data = Session::pull('flash_data');
              @endphp
              toastr.{{ $flash_data['status'] }}("{{ $flash_data['message'] }}");
            @endif

            $('#login-form').validate({
              rules: {
                  email: {
                    required: true,
                    email: true,
                  },
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                  error.addClass('invalid-feedback');
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
   </body>
</html>