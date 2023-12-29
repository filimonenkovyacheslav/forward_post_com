@extends('layouts.front_gcs')

@section('content')

	<section class="app-content">
		<div class="container new-parcel"> 

			<div class="row row-black-button">
				<a class="yellow-button" href="https://www.gcs-deliveries.com/">To Homepage</a>
				<a class="yellow-button" href="{{ route('parcelFormEngGcs') }}">Book Online</a>
			</div>  

				{!! Form::open(['url'=>'https://forward-post.com/api/forward-tracking-form-eng', 'class'=>'form-horizontal','method' => 'GET']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('get_tracking',old('get_tracking'),['class' => 'form-control', 'placeholder' => 'Enter the parcel number', 'required'])!!}
                        </div>
                    </div>
                </div>

                {!! Form::hidden('url_name','https://www.forward-post.com/tracking-form-gcs')!!}

                {!! Form::button('To track the parcel',['class'=>'yellow-button','type'=>'submit']) !!}

                <br>
                <hr>
                <h1 class="tracking-result">Parcel status:</h1>

				@php
	            $get_data = request()->all();
	            @endphp
	            
	            @if (isset($get_data['message']))
	            <h1 class="tracking-result">
	                {{ $get_data['message'] }}
	            </h1>
	            @endif
	            @if (isset($get_data['err_message']))
					<h1 class="tracking-result">Not found</h1>
				@endif 						               
                
                {!! Form::close() !!}
				
				<!-- временное -->
                <br>
                <div>
                    <button class="yellow-button" style="width:300px"><a style="color:#000" href="{{ route('trackingFormGcs') }}">Track another shipment</a></button>
                </div>
                <br>
                <div>
                    <button class="yellow-button" style="width:300px"><a style="color:#000" href="https://www.gcs-deliveries.com/">TO HOMEPAGE</a></button>
                </div> 
				<!-- /временное -->			
		</div>           
	</section><!-- /.app-content -->

@endsection