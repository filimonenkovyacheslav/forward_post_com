@extends('layouts.phil_ind_admin')
@section('content')

@can('editColumns-2')
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

						{!! Form::open(['url'=>route('addPalletData'), 'class'=>'form-horizontal worksheet-add-form','method' => 'POST']) !!}

						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::button('Cancel choose',['class'=>'btn btn-danger', 'onclick' => 'handleCencel()']) !!}						

						<div id="checkbox-group">

							@foreach ($pallet_arr as $number)  
							<div class="form-group">
								{!! Form::label('pallet[]', $number,['class' => 'col-md-2 control-label'])   !!}
								<div class="col-md-1">
									{!! Form::checkbox('pallet[]', $number, '', ['onclick' => 'handleCheckbox(this)']) !!}
								</div>
							</div>
							@endforeach	

						</div>	

						<label class="value-by-pallet">Enter number of lot:
							<input class="form-control" type="text" name="value-by-pallet">
						</label>

						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::button('Cancel choose',['class'=>'btn btn-danger', 'onclick' => 'handleCencel()']) !!}
						{!! Form::close() !!}

					</div>
				</div>
			</div>


		</div>
	</div><!-- .animated -->
</div><!-- .content -->


@else
<h1>Вы не можете просматривать эту страницу!</h1>
@endcan 
@endsection