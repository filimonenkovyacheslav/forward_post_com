<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="alternate" hreflang="uk" href="https://www.dd-cargo.com/?lang=uk">
    <link rel="alternate" hreflang="ru" href="https://www.dd-cargo.com/?lang=ru">
    <link rel="alternate" hreflang="en" href="https://www.dd-cargo.com/?lang=en">
    <link rel="alternate" hreflang="he" href="https://www.dd-cargo.com/?lang=he">
    <link rel="alternate" hreflang="x-default" href="https://www.dd-cargo.com/?lang=en">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <!-- <meta name="csrf-token" content="i9THP6iOBywJ8r8shZ93eEUo1YjJKFoLX0MQPELt">  -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <title>Parcels from/to Israel | DD-CARGO</title>

    <meta name="description" content="Affordable timely shipments of your parcels and documents. We proudly stand behind the commitments we make to our customers.">
    <link rel="canonical" href="https://www.dd-cargo.com?lang=en">
    <meta property="og:title" content="Parcels from/to Israel | DD-CARGO ">
    <meta property="og:description" content="Affordable timely shipments of your parcels and documents. We proudly stand behind the commitments we make to our customers.">
    <meta property="og:url" content="https://www.dd-cargo.com?lang=en">
    <meta property="og:site_name" content="DD-CARGO ">
    <meta property="og:type" content="website">
    <meta name="google-site-verification" content="_5HGCAqwD5YfD0QIz6s1U-43t6s95u0wqpj5n5ufL7o">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="text/javascript">
        var addToTableUrl = "{{ url('/api/add-to-temp-table') }}"
        var getFromTableUrl = "{{ url('/api/get-from-temp-table') }}"
    </script>
    
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>      
    <script src="{{ asset('js/scripts.js') }}" defer></script>
    <script src="{{ asset('assets/js/html2canvas.min.js') }}"></script>
    
</head>
<body>
    <div id="app">

        @yield('content')       

    </div><!-- /#app -->
</body>
</html>