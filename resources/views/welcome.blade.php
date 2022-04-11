@extends('layouts.front')

@section('content')

<ul class="dd-dropdown-menu" style="list-style-type: none;">
    @guest
    <li class="dd-dropdown-menu-li"><a class="black-button" href="{{ route('login') }}">Login</a></li>
    <li class="dd-dropdown-menu-li"><a class="black-button" href="{{ route('register') }}">Register</a></li>
    @else
    <li class="dd-dropdown-menu-li">
        <a class="black-button" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
    @endguest
</ul> 

@endsection