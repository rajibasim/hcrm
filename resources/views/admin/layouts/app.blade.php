<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ config('app.name') }} || {{ $metadata['page_title'] }}</title>
         <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/fontawesome-free/css/all.min.css?version='.config('config.version')) }}" />
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css?version='.config('config.version')) }}" />
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css?version='.config('config.version')) }}" />
        <!-- JQVMap -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/jqvmap/jqvmap.min.css?version='.config('config.version')) }}" />
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('admin-assets/css/adminlte.min.css?version='.config('config.version')) }}" />
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css?version='.config('config.version')) }}" />
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/select2/css/select2.min.css?version='.config('config.version')) }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css?version='.config('config.version')) }}">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/daterangepicker/daterangepicker.css?version='.config('config.version')) }}" />
        <!-- summernote -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.css?version='.config('config.version')) }}" />
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css?version='.config('config.version')) }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/toastr/toastr.min.css?version='.config('config.version')) }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css?version='.config('config.version')) }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css?version='.config('config.version')) }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css?version='.config('config.version')) }}">
        <!-- Ekko Lightbox -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/ekko-lightbox/ekko-lightbox.css?version='.config('config.version')) }}">
        <!-- Date Picker CSS -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <!-- summernote -->
        <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.css?version='.config('config.version')) }}">
        
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{ asset('admin-assets/img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60" width="60" />
            </div>

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">            
                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <!-- Navbar Search -->
                    <li class="nav-item">
                        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                            <i class="fas fa-search"></i>
                        </a>
                        <div class="navbar-search-block">
                            <form class="form-inline">
                                <div class="input-group input-group-sm">
                                    <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search" />
                                    <div class="input-group-append">
                                        <button class="btn btn-navbar" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ url('admin/logout') }}" role="button">
                          <i class="fas fa-power-off"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="{{ url('admin/login') }}" class="brand-link">
                    <img src="{{ asset('admin-assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: 0.8;" />
                    <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="{{ asset('admin-assets/img/avatar.png') }}" class="img-circle elevation-2" alt="User Image" />
                        </div>
                        <div class="info">
                            <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        </div>
                    </div>
                    <!-- SidebarSearch Form -->
                    <div class="form-inline">
                        <div class="input-group" data-widget="sidebar-search">
                          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                          <div class="input-group-append">
                            <button class="btn btn-sidebar">
                              <i class="fas fa-search fa-fw"></i>
                            </button>
                          </div>
                        </div>
                    </div>
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>
                                        Dashboard
                                    </p>
                                </a>
                            </li>
                            @canany(['status_view', 'sales_person_view'])
                                <li class="nav-item {{ Request::is("admin/masterdata*") ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is("admin/masterdata*") ? 'active' : '' }}">
                                        <i class="nav-icon fa fa-list-ul"></i>
                                        <p>
                                            Master Data
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    @can('sales_person_view')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('sales-person.index') }}" class="nav-link {{ Request::is("admin/masterdata/sales-person*") ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Sales Person</p>
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                </li>
                            @endcan
                            @canany(['user_view', 'role_view'])
                                <li class="nav-item {{ Request::is("admin/usersrole*") ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is("admin/usersrole*") ? 'active' : '' }}">
                                        <i class="nav-icon fa fa-list-ul"></i>
                                        <p>
                                            Manage User
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    @can('user_view')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('user.index') }}" class="nav-link {{ Request::is("admin/usersrole/user*") ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>User</p>
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                    @can('role_view')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('role.index') }}" class="nav-link {{ Request::is("admin/usersrole/role*") ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Role</p>
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                </li>
                            @endcan
                            @canany(['customer_view'])
                                <li class="nav-item {{ Request::is("admin/managecustomer*") ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is("admin/managecustomer*") ? 'active' : '' }}">
                                        <i class="nav-icon fa fa-list-ul"></i>
                                        <p>
                                            Manage Customer
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    @can('customer_view')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('customer.index') }}" class="nav-link {{ Request::is("admin/managecustomer/customer*") ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Customer</p>
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                </li>
                            @endcan
                            @canany(['bill_entry_view'])
                                <li class="nav-item {{ Request::is("admin/managebill*") ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is("admin/managebill*") ? 'active' : '' }}">
                                        <i class="nav-icon fa fa-list-ul"></i>
                                        <p>
                                            Manage Bill
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    @can('bill_entry_view')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('bill.index') }}" class="nav-link {{ Request::is("admin/managebill/bill*") ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Bill</p>
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                </li>
                            @endcan
                            @canany(['balance_sheet_view'])
                                <li class="nav-item {{ Request::is("admin/managebalancesheet*") ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ Request::is("admin/managebalancesheet*") ? 'active' : '' }}">
                                        <i class="nav-icon fa fa-list-ul"></i>
                                        <p>
                                            Manage Balance Sheet
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    @can('balance_sheet_view')
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('balance-sheet.index') }}" class="nav-link {{ Request::is("admin/managebalancesheet/balance-sheet*") ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Balance Sheet</p>
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                </li>
                            @endcan 
                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>
            @yield('content')
            <footer class="main-footer">
                <strong>Copyright &copy; {{ date('Y') }} <a href="/dashboard">{{ config('app.name') }}</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block"><b>Current Version</b> {{ config('config.version') }}</div>
            </footer>
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js?version='.config('config.version')) }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('admin-assets/plugins/jquery-ui/jquery-ui.min.js?version='.config('config.version')) }}"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge("uibutton", $.ui.button);
        </script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js?version='.config('config.version')) }}"></script>
        <!-- daterangepicker -->
        <script src="{{ asset('admin-assets/plugins/moment/moment.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/daterangepicker/daterangepicker.js?version='.config('config.version')) }}"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{ asset('admin-assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js?version='.config('config.version')) }}"></script>
        <!-- Summernote -->
        <script src="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.js?version='.config('config.version')) }}"></script>
        <!-- overlayScrollbars -->
        <script src="{{ asset('admin-assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js?version='.config('config.version')) }}"></script>
        <!-- SweetAlert2 -->
        <script src="{{ asset('admin-assets/plugins/sweetalert2/sweetalert2.min.js?version='.config('config.version')) }}"></script>
        <!-- Toastr -->
        <script src="{{ asset('admin-assets/plugins/toastr/toastr.min.js?version='.config('config.version')) }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('admin-assets/js/adminlte.js?version='.config('config.version')) }}"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{ asset('admin-assets/js/demo.js?version='.config('config.version')) }}"></script>
        <!-- jquery-validation -->
        <script src="{{ asset('admin-assets/plugins/jquery-validation/jquery.validate.min.js?version='.config('config.version')) }}"></script>

        <!-- DataTables  & Plugins -->
        <script src="{{ asset('admin-assets/plugins/datatables/jquery.dataTables.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-responsive/js/dataTables.responsive.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-buttons/js/dataTables.buttons.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/jszip/jszip.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/pdfmake/pdfmake.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/pdfmake/vfs_fonts.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-buttons/js/buttons.html5.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-buttons/js/buttons.print.min.js?version='.config('config.version')) }}"></script>
        <script src="{{ asset('admin-assets/plugins/datatables-buttons/js/buttons.colVis.min.js?version='.config('config.version')) }}"></script>
        <!-- dropzonejs -->
        <script src="{{ asset('admin-assets/plugins/dropzone/min/dropzone.min.js?version='.config('config.version')) }}"></script>

        <!-- Select2 -->
        <script src="{{ asset('admin-assets/plugins/select2/js/select2.full.min.js?version='.config('config.version')) }}"></script>
        <!-- Ekko Lightbox -->
        <script src="{{ asset('admin-assets/plugins/ekko-lightbox/ekko-lightbox.min.js?version='.config('config.version')) }}"></script>
        <!-- ChartJS -->
        <script src="{{ asset('admin-assets/plugins/chart.js/Chart.min.js?version='.config('config.version')) }}"></script>
        <!-- Summernote -->
        <script src="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.js?version='.config('config.version')) }}"></script>
        
        <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            //Initialize Select2 Elements
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            $('.datepicker').datepicker({
                inline: true,
                dateFormat: "yy/mm/yy",
                changeFirstDay: false,
                minDate: -7,
                maxDate: +1,
            });

            $('.datepicker3').datepicker({
                inline: true,
                dateFormat: "yy-mm-dd",
                //changeFirstDay: false,
                //minDate: -7,
                //maxDate: +1,
            });

            $('.datepicker2').datepicker({
                inline: true,
                dateFormat: "dd/mm/yy",
            });

            //$( ".datepicker" ).datepicker();

            // Summernote
            $('#summernote').summernote();   

            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
              event.preventDefault();
              $(this).ekkoLightbox({
                alwaysShowClose: true
              });
            });       
        })
        </script>
        @yield('javascripts')
    </body>
</html>
