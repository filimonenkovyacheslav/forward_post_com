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

					@if(isset($receipt))				

						{!! Form::open(['url'=>route('receiptsUpdate', ['id'=>$receipt->id]), 'class'=>'form-horizontal receipt-update-form','method' => 'POST']) !!}

						@if(!$receipt->double)

						<div class="form-group">
							{!! Form::label('sum','Сумма (Sum)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sum',$receipt->sum,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('date','Дата (Date)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-4">
								{!! Form::date('date-input','',['class' => 'form-control'])!!}
							</div>
							<div class="col-md-4">
								{!! Form::text('date-view',$receipt->date,['class' => 'form-control', 'disabled' => 'disabled'])!!}
							</div>
								{!! Form::hidden('date',$receipt->date)!!}
						</div>

						@endif
						
						<div class="form-group">
							{!! Form::label('tracking_main','Номер посылки (Tracking number)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',$receipt->tracking_main,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('courier_name','Имя курьера (Courier name)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('courier_name',$receipt->courier_name,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comment','Комментарий (Comment)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comment',$receipt->comment,['class' => 'form-control'])!!}
							</div>
						</div>

						{!! Form::hidden('id',$receipt->id)!!}

						{!! Form::hidden('range_number',$receipt->range_number)!!}

						{!! Form::hidden('double',$receipt->double)!!}

						{!! Form::hidden('legal_entity',$receipt->legal_entity)!!}

						{!! Form::hidden('receipt_number',$receipt->receipt_number)!!}
					
						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

					@endif
					
					</div>
				</div>
			</div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

<script type="text/javascript">
	const input = document.querySelector('[name="date-input"]');
	const view = document.querySelector('[name="date-view"]');
	const date = document.querySelector('[name="date"]');
	input.oninput = function() {
		let value = input.value.split('-');
		value = value[0].slice(2)+value[1]+value[2];
		view.value = value;
		date.value = value;
	};
</script>

@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection