@extends('layouts.front')

@section('content')

	<section class="app-content">
		<div class="container new-parcel"> 

			<div class="row row-black-button">
				<a class="black-button" href="https://www.orientalexp.com/">Вернуться на главную</a>
				<a class="black-button" href="{{ route('parcelForm') }}">Оформить посылку</a>
			</div>  

				{!! Form::open(['url'=>'https://ddcargos.com/api/forward-tracking-form', 'class'=>'form-horizontal','method' => 'GET']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('get_tracking',old('get_tracking'),['class' => 'form-control', 'placeholder' => 'Введите № трекинга', 'required'])!!}
                        </div>
                    </div>
                </div>
                {!! Form::hidden('url_name','https://www.forward-post.com/tracking-ru-form')!!}

                {!! Form::button('Проверить',['class'=>'btn','type'=>'submit']) !!}

                <br>
                <h1 class="tracking-result">Статус посылки:</h1>

				@php
	            $get_data = request()->all();
	            @endphp
	            
	            @if (isset($get_data['message']))
	            <h1 class="tracking-result">
	                {{ $get_data['message'] }}
	            </h1>
	            @endif
	            @if (isset($get_data['err_message']))
	            <h1 class="tracking-result">Не найдено</h1>
	            @endif					               
	                
                {!! Form::close() !!}
				
				<!-- временное -->
                <br>
                <div>
                    <button class="btn btn-default" style="width:200px"><a style="color:#000" href="{{ route('trackingRuForm') }}">Отследить еще посылку</a></button>
                </div>
                <br>
                <div>
                    <button class="btn btn-default" style="width:200px"><a style="color:#000" href="https://www.orientalexp.com/">На главную</a></button>
                </div> 
				<!-- /временное -->			
		</div>           
	</section><!-- /.app-content -->

@endsection