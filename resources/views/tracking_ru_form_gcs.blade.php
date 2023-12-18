@extends('layouts.front_gcs')

@section('content')

	<section class="app-content">
		<div class="container new-parcel"> 

			<div class="row row-black-button">
				<a class="yellow-button" href="https://www.gcs-deliveries.com/">На главную</a>
				<a class="yellow-button" href="{{ route('parcelFormGcs') }}">Оформить посылку</a>
			</div>  

				{!! Form::open(['url'=>'https://ddcargos.com/api/forward-tracking-form', 'class'=>'form-horizontal','method' => 'GET']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('get_tracking',old('get_tracking'),['class' => 'form-control', 'placeholder' => 'Введите № трекинга', 'required'])!!}
                        </div>
                    </div>
                </div>
                {!! Form::hidden('url_name','https://www.forward-post.com/tracking-ru-form-gcs')!!}

                {!! Form::button('Проверить',['class'=>'yellow-button','type'=>'submit']) !!}

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
                    <button class="yellow-button" style="width:200px"><a style="color:#000" href="{{ route('trackingRuFormGcs') }}">Отследить еще</a></button>
                </div>
                <br>
                <div>
                    <button class="yellow-button" style="width:200px"><a style="color:#000" href="https://www.gcs-deliveries.com/">На главную</a></button>
                </div> 
				<!-- /временное -->			
		</div>           
	</section><!-- /.app-content -->

@endsection