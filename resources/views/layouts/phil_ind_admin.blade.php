<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token ()}}">

    <link rel="apple-touch-icon" href="{{ asset('apple-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/scss/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/vector-map/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->

    <style type="text/css">
        #farms .dropdown-submenu, .category_arr .dropdown-submenu{
            left: -25%;
        }
        #farms .dropdown-submenu>.dropdown-menu, .category_arr .dropdown-submenu>.dropdown-menu{
            position: relative;
            left: 10%;
            background-color: #212529;
        }  
        #farms .dropdown-submenu:hover>.dropdown-menu, .category_arr .dropdown-submenu:hover>.dropdown-menu{
            display: block;
        }
        #farms ul.sub-menu.children.dropdown-menu, .menu-item-has-children.dropdown ul.sub-menu.children.dropdown-menu{
            max-height: max-content;
        }

        #main-menu .dropdown .btn-dropdown, #main-menu .dropdown ul.dropdown-menu{
            background-color: #212529;
        }
        .navbar .navbar-nav li > a.btn-dropdown {
           width: auto; 
        }
    </style>

    <script type="text/javascript">
        var createTableUrl = "{{ url('/create-temp-table') }}"
        var userName = "{{ Auth::user()->name }}"
        var couriersArr = '@php echo($couriers_arr) @endphp'
    </script>

</head>
<body>   

    <!-- Left Panel -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="/">DD-CARGO</a>
                <a class="navbar-brand hidden" href="/">DD</a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">

                    @can('eng-view-post')
                    
                    <li class="active">
                        <a href="{{route('adminPhilIndIndex')}}"> <i class="menu-icon fa fa-dashboard"></i>Control Panel </a>
                    </li>                 
                                    
                    <li>
                        <a href="{{route('adminPhilIndUsers')}}"><i class="menu-icon fa fa-address-card "></i> Users </a>
                    </li>

                    <li>
                        <a href="{{route('adminCourierEngDraftWorksheet')}}"><i class="menu-icon fa fa-archive "></i> Draft </a>
                    </li>

                    <li>
                        <a href="{{route('adminPhilIndWorksheet')}}"><i class="menu-icon fa fa-archive "></i> Work sheet </a>
                    </li>

                    @endcan

                    @can('editPost')
                    <li class="dropdown">
                        <a class="btn btn-dropdown dropdown-toggle" type="button" data-toggle="dropdown">Receipts
                            <span class="caret"></span>
                            <i class="menu-icon fa fa-book "></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{url('/admin/receipts/dd')}}"> Квитанции ДД (Receipts DD) </a></li>
                            <li><a href="{{url('/admin/receipts/ul')}}"> Квитанции ЮЛ (Receipts UL) </a></li>
                            <li><a href="{{route('adminReceiptsArchive')}}"></i> Notifications </a></li>
                        </ul>
                    </li>                     
                    @endcan

                    @can('editColumns-eng')
                    <li class="dropdown">
                        <a class="btn btn-dropdown dropdown-toggle" type="button" data-toggle="dropdown">Packing Lists
                            <span class="caret"></span>
                            <i class="menu-icon fa fa-book "></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('indexPackingEngNew')}}"> New Packing List </a></li>
                            <li><a href="{{route('indexPackingEng')}}"> Old Packing List </a></li>
                        </ul>
                    </li> 
                    @endcan 
                    
                    @can('editColumns-2')
                    <li>
                        <a href="{{route('adminWarehouse')}}"><i class="menu-icon fa fa-book "></i> Warehouse </a>
                    </li> 
                    @endcan 

                    @can('view-post')
                    <li>
                        <a href="{{route('adminIndex')}}"><i class="menu-icon fa fa-book "></i> Russian admin </a>
                    </li>
                    @endcan  

                    @can('china-view-post')
                    <li>
                        <a href="{{route('adminChinaIndex')}}"><i class="menu-icon fa fa-book "></i> China admin </a>
                    </li>
                    @endcan      

                    @can('editCourierTasks')
                    <li>
                        <a href="{{route('adminCourierTask')}}"><i class="menu-icon fa fa-book "></i> Couriers Tasks</a>
                    </li> 
                    @endcan   

                    @can('changeColor')
                    <li>
                        <a href="{{route('adminTrash')}}"><i class="menu-icon fa fa-book "></i> Корзина/Trash</a>
                    </li> 
                    @endcan        

                    @can('update-user')
                    <li>
                        <a href="{{route('adminUpdatesArchive')}}"><i class="menu-icon fa fa-book "></i> Updates Archive</a>
                    </li> 
                    @endcan 

                    @can('editEngDraft')
                    <li>
                        <a href="{{route('tempLinks')}}"><i class="menu-icon fa fa-book "></i> Temporary links</a>
                    </li> 
                    @endcan 

                    @can('editColumns-2')
                    <li>
                        <a href="{{route('showPalletData')}}"><i class="menu-icon fa fa-book "></i> Pallets </a>
                    </li> 
                    @endcan 

                    @can('changeColor')
                    <li>
                        <a href="{{route('adminLog')}}"><i class="menu-icon fa fa-book "></i> Logs</a>
                    </li> 
                    @endcan 

                    @can('changeColor')
                    <li>
                        <a href="{{route('generalSearchShow')}}"><i class="menu-icon fa fa-book "></i> General Search</a>
                    </li> 
                    @endcan 
                
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-10">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                </div>

                <div class="col-sm-2">
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @endguest
                </ul>
            </div>

        </div>

    </header><!-- /header -->
    <!-- Header-->

    @yield('content')

    <!-- Modal -->
    <a id="double-qty" data-toggle="modal" data-target="#doubleQty"></a>

    <div class="modal fade" id="doubleQty" tabindex="-1" role="dialog" aria-labelledby="doubleQtyLabel" aria-hidden="true" style="background: rgba(0, 0, 0, 0.4);">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="doubleQtyLabel">Duplicate qty</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <label>Enter qty
                    <input type="number" name="double_qty" min="1" value="1">
                </label>
                <button id="add_double_qty" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

</div><!-- /#right-panel -->

<!-- Right Panel -->

<script src="{{ asset('assets/js/vendor/jquery-2.1.4.min.js') }}"></script>
<!-- <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js') }}"></script> -->
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>


<script src="{{ asset('assets/js/lib/data-table/datatables-eng.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/data-table/datatables-init.js') }}"></script>

<script src="{{ asset('assets/js/admin-scripts.js') }}"></script>

</body>
</html>

