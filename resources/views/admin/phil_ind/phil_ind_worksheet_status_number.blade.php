@extends('layouts.phil_ind_admin')
@section('content')

@can('editPost')
<!-- <div class="breadcrumbs">
	<div class="col-sm-4">
		<div class="page-header float-left">
			<div class="page-title">
				<h1>Панель управления</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="page-header float-right">
			<div class="page-title">
				<ol class="breadcrumb text-right">
					<li><a href="{{route('adminIndex')}}">Панель управления</a></li>
					<li class="active">{{ $title }}</li>
				</ol>                        
			</div>
		</div>
	</div>
</div> -->
<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>
					<div class="card-body">					

						{!! Form::open(['url'=>route('changePhilIndStatus'), 'class'=>'form-horizontal china-worksheet-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('lot_number','Choose lot number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('lot_number', $number_arr, '',['class' => 'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('status','Status',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Pending' => 'Pending', 'Forwarding to the warehouse in the sender country' => 'Forwarding to the warehouse in the sender country', 'At the warehouse in the sender country' => 'At the warehouse in the sender country', 'At the customs in the sender country' => 'At the customs in the sender country', 'Forwarding to the receiver country' => 'Forwarding to the receiver country', 'At the customs in the receiver country' => 'At the customs in the receiver country', 'Forwarding to the receiver' => 'Forwarding to the receiver', 'Delivered' => 'Delivered', 'Return' => 'Return', 'Box' => 'Box', 'Pick up' => 'Pick up', 'Specify' => 'Specify', 'Think' => 'Think', 'Canceled' => 'Canceled'), '',['class' => 'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('status_he_disabled','Status He',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',old('status_he'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',old('status_he'))!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status_ru_disabled','Status Ru',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ru_disabled',old('status_ru'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ru',old('status_ru'))!!}
							</div>
						</div>

						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

					</div>
				</div>
			</div>


		</div>
	</div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection