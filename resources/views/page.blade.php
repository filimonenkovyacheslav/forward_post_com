@extends('layouts.front')

@section('content')

    <section class="app-content page-bg">
        <div class="container">
            <div class="front-page">
                @if($article !== null)
                <h1>{{ $article->title }}</h1>
                <div>{!! $article->text !!}</div>
                @endif                            
            </div>
        </div>           
    </section><!-- /.app-content -->

@endsection
