@extends('layouts.front')

@section('content')

	<section class="app-content">
		<div class="container new-parcel"> 

			<div class="row row-black-button">
				<a class="black-button" href="https://www.orientalexp.com/">To Homepage</a>
				<a class="black-button" href="{{ route('philIndParcelForm') }}">Book Online</a>
			</div>  

				{!! Form::open(['url'=>route('getTracking'), 'class'=>'form-horizontal','method' => 'POST']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('get_tracking',old('get_tracking'),['class' => 'form-control', 'placeholder' => 'Enter the parcel number', 'required'])!!}
                        </div>
                    </div>
                </div>

                {!! Form::button('To track the parcel',['class'=>'btn','type'=>'submit']) !!}

                <br>
                <h1 class="tracking-result">Parcel status:</h1>

				@if (session('message_en'))
					<h1 class="tracking-result">
						{{ session('message_en') }}
					</h1>	
				@elseif (session('not_found'))
					<h1 class="tracking-result">Not found</h1>
				@endif 						               
                
                {!! Form::close() !!}
				
				<!-- временное -->
                <br>
                <div>
                    <button class="btn btn-default" style="width:200px"><a style="color:#000" href="{{ route('trackingForm') }}">Track another shipment</a></button>
                </div>
                <br>
                <div>
                    <button class="btn btn-default" style="width:200px"><a style="color:#000" href="https://www.orientalexp.com/">TO HOMEPAGE</a></button>
                </div> 
				<!-- /временное -->			
		</div>           
	</section><!-- /.app-content -->

@endsection