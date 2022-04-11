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
    <meta name="csrf-token" content="i9THP6iOBywJ8r8shZ93eEUo1YjJKFoLX0MQPELt">

    <title>{{__('front.title')}} | DD-CARGO</title>

    <meta name="description" content="Посылки из Израиля и в Израиль морскими контейнерами и авиа с DD-CARGO - это надежно и недорого ">
    <link rel="canonical" href="https://www.dd-cargo.com?lang=ru">
    <meta property="og:title" content="Посылки из/в Израиль | DD-CARGO ">
    <meta property="og:description" content="Посылки из Израиля и в Израиль морскими контейнерами и авиа с DD-CARGO - это надежно и недорого ">
    <meta property="og:url" content="https://www.dd-cargo.com?lang=ru">
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
    
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script src="{{ asset('js/scripts.js') }}" defer></script>
    
</head>
<body>
    <div id="app">

        <header>

            <div class="container">
                <div class="row first-row">

                    <div class="col-md-2 guarantee">
                        <a href="#" aria-label="ГАРАНТИЯ">
                            <div class="style-guarantee">
                                <span>{{__('front.warranty')}}</span>                                        
                            </div>   
                        </a>
                    </div>
                    
                    <div class="col-md-3 tracking">
                        <a href="{{ route('trackingForm') }}" aria-label="ОТСЛЕДИТЬ ПОСЫЛКУ">
                            <div class="style-tracking">
                                <span>{{__('front.track_parcel')}}</span> 
                            </div>           
                        </a>
                    </div>
                    
                    <div class="col-md-2 home-icon">
                        <a href="{{ route('welcome') }}" title="" class="image-button-link">
                            <div class="image-button"></div>
                        </a>
                    </div>
                    
                    <div class="col-md-3 ask">
                        <a href="#" aria-label="ЗАДАЙТЕ ВОПРОС">
                            <div class="style-ask">
                                <span>{{__('front.ask')}}</span>
                            </div>    
                        </a>    
                    </div>

                    <div class="col-md-2 language-menu">
                        <div role="menu" class="language-list">

                            <div onclick="languageParent(this)" data-locale="{{route('setlocale', ['lang' => 'ru'])}}" class="list-russian {{ (App\Http\Middleware\LocaleMiddleware::getLocale() === null) ? 'active-language' : '' }}">
                                <img onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'ru'])}}" class="image-russian" src="{{ asset('images/flag-ru.webp') }}">
                                <div onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'ru'])}}" class="button-russian" aria-label="Russian" role="link" >Russian</div>
                            </div>

                            <div onclick="languageParent(this)" data-locale="{{route('setlocale', ['lang' => 'en'])}}" class="list-english {{ (App\Http\Middleware\LocaleMiddleware::getLocale() === 'en') ? 'active-language' : '' }}">
                                <img onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'en'])}}" src="{{ asset('images/flag-us.png') }}"  class="image-english">
                                <div onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'en'])}}" class="button-english" aria-label="English" tabindex="-1" role="link">English</div>
                            </div> 

                            <div onclick="languageParent(this)" data-locale="{{route('setlocale', ['lang' => 'he'])}}" class="list-hebrew {{ (App\Http\Middleware\LocaleMiddleware::getLocale() === 'he') ? 'active-language' : '' }}">
                                <img onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'he'])}}" src="{{ asset('images/flag-he.png') }}"  class="image-hebrew">
                                <div onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'he'])}}" class="button-hebrew" aria-label="Hebrew" tabindex="-1" role="link">Hebrew</div>
                            </div> 

                            <div onclick="languageParent(this)" data-locale="{{route('setlocale', ['lang' => 'uk'])}}" class="list-ukrainian {{ (App\Http\Middleware\LocaleMiddleware::getLocale() === 'uk') ? 'active-language' : '' }}">
                                <img onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'uk'])}}" src="{{ asset('images/flag-ua.png') }}"  class="image-ukrainian">
                                <div onclick="languageChild(this)" data-locale="{{route('setlocale', ['lang' => 'uk'])}}" class="button-ukrainian" aria-label="Ukrainian" tabindex="-1" role="link">Ukrainian</div>
                            </div>

                        </div>
                    </div><!-- /.language-menu -->

                </div><!-- /.first-row -->
            </div><!-- /.container -->
            
            <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
                <div class="container">
                    <button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler collapsed"><span class="navbar-toggler-icon"></span></button> 
                    <div id="navbarSupportedContent" class="navbar-collapse collapse" style="">
                        <!-- <ul class="navbar-nav mr-auto"></ul>  -->
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-send nav-item">
                                <a class="nav-link send" href="#">{{__('front.send_parcel')}}</a>
                                <ul class="dd-dropdown-menu">
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.to_ukraine')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.from_ukraine')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.to_russia')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.from_russia')}}</a></li>
                                </ul>    
                            </li>
                            <li class="nav-prise nav-item">
                                <a class="nav-link prise" href="#">{{__('front.price')}}</a>
                                <ul class="dd-dropdown-menu">
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.to_russia')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.from_russia')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.from_ukraine')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.to_ukraine')}}</a></li>
                                </ul>    
                            </li>
                            <li class="nav-rule nav-item">
                                <a class="nav-link rule" href="#">{{__('front.rules')}}</a>
                                <ul class="dd-dropdown-menu">
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.russia')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.israel')}}</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="#">{{__('front.ukraine')}}</a></li>
                                </ul>    
                            </li>
                            <li class="nav-questions nav-item">
                                <a class="nav-link questions" href="#">{{__('front.faq')}}</a>
                            </li>
                            <li class="nav-contacts nav-item">
                                <a class="nav-link contacts" href="#">{{__('front.contacts')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{__('front.reviews')}}</a>
                            </li>                            
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{__('front.account')}}</a>
                                <ul class="dd-dropdown-menu">
                                    @guest
                                    <li class="dd-dropdown-menu-li"><a href="{{ route('login') }}">Login</a></li>
                                    <li class="dd-dropdown-menu-li"><a href="{{ route('register') }}">Register</a></li>
                                    @else
                                    <li class="dd-dropdown-menu-li">
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                    @endguest
                                </ul> 
                            </li> 
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        @yield('content')
        
        <footer>
            <p><span class="facebook-icon"><i class="fa fa-facebook-square" aria-hidden="true"></i></span></p>
            <p class="footer-title">DD-CARGO *{{__('front.title')}}</p>
            <p class="privacy-policy"><img src="{{ asset('images/pdf.webp') }}"><span>{{__('front.policy')}}</span></p>
        </footer>

    </div><!-- /#app -->
</body>
</html>