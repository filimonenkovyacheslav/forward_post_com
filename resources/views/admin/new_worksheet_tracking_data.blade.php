@extends('layouts.admin')
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

						{!! Form::open(['url'=>route('addNewData'), 'class'=>'form-horizontal worksheet-add-form','method' => 'POST']) !!}

						{!! Form::button('Сохранить',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::button('Отменить выделение',['class'=>'btn btn-danger', 'onclick' => 'handleCencel()']) !!}						

						<div id="checkbox-group">

						@foreach ($date_arr as $number)  
							<div class="form-group">
							{!! Form::label('tracking[]', $number,['class' => 'col-md-2 control-label'])   !!}
								<div class="col-md-1">
								{!! Form::checkbox('tracking[]', $number, '', ['onclick' => 'handleCheckbox(this)']) !!}
								</div>
							</div>
						@endforeach	

						</div>	

						<label>Выберите колонку:
							<select class="form-control" id="tracking-columns" name="tracking-columns">
								<option value="" selected="selected"></option>
								<option value="site_name">Сайт</option>
								<option value="date">Дата</option>
								<option value="direction">Направление</option>
								<option value="status">Статус</option>
								<option value="partner">Партнер</option>
								<option value="tracking_local">Локальный</option>
								<option value="tracking_transit">Транзитный</option>
								<option value="pallet_number">Номер паллеты</option>
								<option value="comment_2">Коммент</option>
								<option value="comments">Комментарии</option>
								<option value="sender_passport">Номер паспорта отправителя</option>
								<option value="recipient_passport">Номер паспорта получателя</option>
								<option value="recipient_email">E-mail получателя</option>
								<option value="package_cost">Стоимость посылки</option>
								<option value="courier">Курьер</option>
								<option value="pick_up_date">Дата забора и комментарии</option>
								<option value="weight">Вес посылки</option>
								<option value="width">Ширина</option>
								<option value="height">Высота</option>
								<option value="length">Длина</option>
								<option value="volume_weight">Объемный вес</option>
								<option value="quantity_things">Кол-во предметов</option>
								<option value="batch_number">Партия</option>
								<option value="pay_date">Дата оплаты и комментарии</option>
								<option value="pay_sum">Сумма оплаты</option>  
							</select>
						</label>	

						<label class="value-by-tracking">Введите значение:
							<input class="form-control" type="text" name="value-by-tracking">
							<input type="hidden" name="status_en">
							<input type="hidden" name="status_ua">
							<input type="hidden" name="status_he">
						</label>							

						{!! Form::button('Сохранить',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::button('Отменить выделение',['class'=>'btn btn-danger', 'onclick' => 'handleCencel()']) !!}
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