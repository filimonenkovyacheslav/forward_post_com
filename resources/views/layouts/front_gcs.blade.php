<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="alternate" hreflang="en" href="https://www.gcs-deliveries.com/">
    <link rel="alternate" hreflang="x-default" href="https://www.gcs-deliveries.com/">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="i9THP6iOBywJ8r8shZ93eEUo1YjJKFoLX0MQPELt">

    <title>Parcels from/to Israel | GCS-DELIVERIES</title>

    <meta name="description" content="Affordable timely shipments of your parcels and documents. We proudly stand behind the commitments we make to our customers.">
    <link rel="canonical" href="https://www.gcs-deliveries.com/">
    <meta property="og:title" content="GCS-DELIVERIES">
    <meta property="og:description" content="Affordable timely shipments of your parcels and documents. We proudly stand behind the commitments we make to our customers.">
    <meta property="og:url" content="https://www.gcs-deliveries.com/">
    <meta property="og:site_name" content="GCS-DELIVERIES">
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
    
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>      
    <script src="{{ asset('js/scripts.js') }}" defer></script>
    
</head>
<body>
    <div id="app" style="background:#fff">

        <header class="gcs-header">           
        </header>

        @yield('content')
        
        <footer class="gcs-footer">
        </footer>

    </div><!-- /#app -->
</body>
</html>