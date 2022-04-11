@extends('layouts.admin')
@section('content')

@can('update-post')

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

					@if(isset($new_worksheet))				

						{!! Form::open(['url'=>route('newWorksheetUpdate', ['id'=>$new_worksheet->id]), 'class'=>'form-horizontal worksheet-update-form','method' => 'POST']) !!}

						@if($user->role === 'office_ru')
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Поле</th>
									<th>Данные</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><div style="width:200px">Сайт</div></td>
									<td><div style="width:900px">{{$new_worksheet->site_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Направление</div></td>
									<td><div style="width:900px">{{$new_worksheet->direction}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Тариф</div></td>
									<td><div style="width:900px">{{$new_worksheet->tariff}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Статус</div></td>
									<td><div style="width:900px">{{$new_worksheet->status}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Трекинг Основной</div></td>
									<td><div style="width:900px">{{$new_worksheet->tracking_main}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Трекинг Локальные</div></td>
									<td><div style="width:900px">{{$new_worksheet->tracking_local}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Номер паллеты</div></td>
									<td><div style="width:900px">{{$new_worksheet->pallet_number}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">OFF Коммент</div></td>
									<td><div style="width:900px">{{$new_worksheet->comment_2}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">DIR Комментарии</div></td>
									<td><div style="width:900px">{{$new_worksheet->comments}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Отправитель</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Страна отправителя</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_country}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Город отправителя</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_city}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Индекс отправителя</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_postcode}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Адрес отправителя</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_address}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Телефон отправителя (стандарт)</div></td>
									<td><div style="width:900px">{{$new_worksheet->standard_phone}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Телефон отправителя (дополнительно)</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_phone}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Номер паспорта отправителя</div></td>
									<td><div style="width:900px">{{$new_worksheet->sender_passport}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Получатель</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Страна получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_country}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Регион</div></td>
									<td><div style="width:900px">{{$new_worksheet->region}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Район</div></td>
									<td><div style="width:900px">{{$new_worksheet->district}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Город получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_city}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Индекс получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_postcode}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Улица получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_street}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Номер дома получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_house}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">корпус</div></td>
									<td><div style="width:900px">{{$new_worksheet->body}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Номер квартиры получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_room}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Телефон получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_phone}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Номер паспорта получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_passport}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">E-mail получателя</div></td>
									<td><div style="width:900px">{{$new_worksheet->recipient_email}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Содержимое посылки</div></td>
									<td><div style="width:900px">{{$new_worksheet->package_content}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Декларируемая стоимость посылки</div></td>
									<td><div style="width:900px">{{$new_worksheet->package_cost}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Курьер</div></td>
									<td><div style="width:900px">{{$new_worksheet->courier}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Дата забора и комментарии</div></td>
									<td><div style="width:900px">{{$new_worksheet->pick_up_date}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Дата оплаты и комментарии</div></td>
									<td><div style="width:900px">{{$new_worksheet->pay_date}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Сумма оплаты</div></td>
									<td><div style="width:900px">{{$new_worksheet->pay_sum}}</div></td>
								</tr>
							</tbody>
						</table>
						@endif

						@can('editPost')

						<div class="form-group">
							{!! Form::label('site_name','Сайт',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('site_name', array('DD-C' => 'DD-C', 'For' => 'For'), $new_worksheet->site_name,['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('direction','Направление',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('direction',$new_worksheet->direction,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tariff','Тариф',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('tariff', array('' => '', 'Море' => 'Море', 'Авиа' => 'Авиа'), $new_worksheet->tariff,['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status','Статус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Доставляется на склад в стране отправителя' => 'Доставляется на склад в стране отправителя', 'На складе в стране отправителя' => 'На складе в стране отправителя', 'На таможне в стране отправителя' => 'На таможне в стране отправителя', 'Доставляется в страну получателя' => 'Доставляется в страну получателя', 'На таможне в стране получателя' => 'На таможне в стране получателя', 'Доставляется получателю' => 'Доставляется получателю', 'Доставлено' => 'Доставлено', 'Возврат' => 'Возврат', 'Коробка' => 'Коробка', 'Забрать' => 'Забрать', 'Уточнить' => 'Уточнить', 'Думают' => 'Думают', 'Отмена' => 'Отмена'), $new_worksheet->status,['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('partner','Партнер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('partner', array('' => '', 'viewer_1' => 'viewer_1', 'viewer_2' => 'viewer_2', 'viewer_3' => 'viewer_3', 'viewer_4' => 'viewer_4', 'viewer_5' => 'viewer_5'), $new_worksheet->partner,['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tracking_main','Трекинг Основной',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',$new_worksheet->tracking_main,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tracking_local','Трекинг Локальные',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_local',$new_worksheet->tracking_local,['class' => 'form-control'])!!}
							</div>
						</div>
												
						<div class="form-group">
							{!! Form::label('tracking_transit','Трекинг Транзитные',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_transit',$new_worksheet->tracking_transit,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editColumns-3')

						<div class="form-group">
							{!! Form::label('pallet_number','Номер паллеты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pallet_number',$new_worksheet->pallet_number,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editColumns-1')
						
						<div class="form-group">
							{!! Form::label('comment_2','OFF Коммент',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comment_2',$new_worksheet->comment_2,['class' => 'form-control'])!!}
							</div>
						</div>
						
						@endcan

						@can('editColumns-4')

						<div class="form-group">
							{!! Form::label('comments','DIR Комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments',$new_worksheet->comments,['class' => 'form-control'])!!}
							</div>
						</div>
						
						@endcan

						@can('editPost')

						<div class="form-group">
							{!! Form::label('sender_name','Отправитель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_name',$new_worksheet->sender_name,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_country','Страна отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_country',$new_worksheet->sender_country,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_city','Город отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-4">
								{!! Form::select('choose_city_ru', ['0' => 'Метод изменения города', '1' => 'Выбрать из списка (автоматически определится Регион)', '2' => 'Ввести вручную (Регион возможно не определится)'],'0',['class' => 'form-control']) !!}
							</div>
							<div class="col-md-4 choose-city-ru">
								@if (in_array($new_worksheet->sender_city, array_keys($israel_cities)))
								
								{!! Form::select('sender_city', $israel_cities, isset($new_worksheet->sender_city) ? $new_worksheet->sender_city : '',['class' => 'form-control']) !!}

								{!! Form::text('sender_city',$new_worksheet->sender_city,['class' => 'form-control','style' => 'display:none','disabled' => 'disabled'])!!}
								
								@else

								{!! Form::select('sender_city', $israel_cities, isset($new_worksheet->sender_city) ? $new_worksheet->sender_city : '',['class' => 'form-control','style' => 'display:none','disabled' => 'disabled']) !!}

								{!! Form::text('sender_city',$new_worksheet->sender_city,['class' => 'form-control'])!!}

								@endif

							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_postcode','Индекс отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_postcode',$new_worksheet->sender_postcode,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_address','Адрес отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_address',$new_worksheet->sender_address,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('standard_phone','Телефон отправителя (стандарт)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('standard_phone',$new_worksheet->standard_phone,['class' => 'form-control standard-phone'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_phone','Телефон отправителя (дополнительно)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_phone',$new_worksheet->sender_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_passport','Номер паспорта отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_passport',$new_worksheet->sender_passport,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_name','Получатель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_name',$new_worksheet->recipient_name,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_country','Страна получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_country',$new_worksheet->recipient_country,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('region','Регион',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('region',$new_worksheet->region,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('district','Район',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('district',$new_worksheet->district,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_city','Город получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_city',$new_worksheet->recipient_city,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_postcode','Индекс получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_postcode',$new_worksheet->recipient_postcode,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_street','Улица получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_street',$new_worksheet->recipient_street,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_house','Номер дома получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_house',$new_worksheet->recipient_house,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('body','корпус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('body',$new_worksheet->body,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_room','Номер квартиры получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_room',$new_worksheet->recipient_room,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_phone','Телефон получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_phone',$new_worksheet->recipient_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_passport','Номер паспорта получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_passport',$new_worksheet->recipient_passport,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_email','E-mail получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_email',$new_worksheet->recipient_email,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editColumns-3')
						
						<div class="form-group">
							{!! Form::label('package_content','Содержимое посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('package_content',$new_worksheet->package_content,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editPost')
						
						<div class="form-group">
							{!! Form::label('package_cost','Декларируемая стоимость посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('package_cost',$new_worksheet->package_cost,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('courier','Курьер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('courier',$new_worksheet->courier,['class' => 'form-control'])!!}
							</div>
						</div>						
						
						<div class="form-group">
							{!! Form::label('pick_up_date','Дата забора и комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pick_up_date',$new_worksheet->pick_up_date,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editColumns-2')
						
						<div class="form-group">
							{!! Form::label('weight','Вес посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',$new_worksheet->weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('width','Ширина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',$new_worksheet->width,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('height','Высота',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',$new_worksheet->height,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('length','Длина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',$new_worksheet->length,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('volume_weight','Объемный вес',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',$new_worksheet->volume_weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('quantity_things','Кол-во предметов',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('quantity_things',$new_worksheet->quantity_things,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editPost')
						
						<div class="form-group">
							{!! Form::label('batch_number','Партия',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('batch_number',$new_worksheet->batch_number,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pay_date','Дата оплаты и комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pay_date',$new_worksheet->pay_date,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pay_sum','Сумма оплаты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pay_sum',$new_worksheet->pay_sum,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_en_disabled','Статус (ENG)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_en_disabled',$new_worksheet->status_en,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_en',$new_worksheet->status_en,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_he_disabled','Статус (HE)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',$new_worksheet->status_he,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',$new_worksheet->status_he,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_ua_disabled','Статус (UA)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ua_disabled',$new_worksheet->status_ua,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ua',$new_worksheet->status_ua,[])!!}
							</div>
						</div>

						@if($new_column_1)
						<div class="form-group">
							{!! Form::label('new_column_1','Дополнительная колонка 1',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_1',$new_worksheet->new_column_1,['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_2)
						<div class="form-group">
							{!! Form::label('new_column_2','Дополнительная колонка 2',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_2',$new_worksheet->new_column_2,['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_3)
						<div class="form-group">
							{!! Form::label('new_column_3','Дополнительная колонка 3',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_3',$new_worksheet->new_column_3,['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_4)
						<div class="form-group">
							{!! Form::label('new_column_4','Дополнительная колонка 4',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_4',$new_worksheet->new_column_4,['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_5)
						<div class="form-group">
							{!! Form::label('new_column_5','Дополнительная колонка 5',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_5',$new_worksheet->new_column_5,['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						<div class="form-group">
							{!! Form::label('recipient_name_customs','Получатель (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_name_customs',$new_worksheet->recipient_name_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_country_customs','Страна получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_country_customs',$new_worksheet->recipient_country_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_city_customs','Город получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_city_customs',$new_worksheet->recipient_city_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_postcode_customs','Индекс получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_postcode_customs',$new_worksheet->recipient_postcode_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_street_customs','Улица получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_street_customs',$new_worksheet->recipient_street_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_house_customs','Номер дома получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_house_customs',$new_worksheet->recipient_house_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_room_customs','Номер квартиры получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_room_customs',$new_worksheet->recipient_room_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_phone_customs','Телефон получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_phone_customs',$new_worksheet->recipient_phone_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_passport_customs','Номер паспорта получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_passport_customs',$new_worksheet->recipient_passport_customs,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

							{!! Form::hidden('id',$new_worksheet->id)!!}

							{!! Form::hidden('shipper_region',$new_worksheet->shipper_region)!!}

							{!! Form::hidden('in_trash',$new_worksheet->in_trash)!!}

							{!! Form::hidden('background',$new_worksheet->background)!!}

							{!! Form::hidden('status_date',$new_worksheet->status_date)!!}

							{!! Form::hidden('site_name',$new_worksheet->site_name,['class' => 'form-control'])!!}

							{!! Form::hidden('date',$new_worksheet->date,['class' => 'form-control'])!!}

							{!! Form::hidden('direction',$new_worksheet->direction,['class' => 'form-control'])!!}

							{!! Form::hidden('tariff',$new_worksheet->tariff,['class' => 'form-control'])!!}

							{!! Form::hidden('status',$new_worksheet->status,['class' => 'form-control'])!!}

							{!! Form::hidden('partner',$new_worksheet->partner,['class' => 'form-control'])!!}

							{!! Form::hidden('tracking_main',$new_worksheet->tracking_main,['class' => 'form-control'])!!}

							{!! Form::hidden('order_number',$new_worksheet->order_number)!!}

							{!! Form::hidden('tracking_local',$new_worksheet->tracking_local,['class' => 'form-control'])!!}

							{!! Form::hidden('tracking_transit',$new_worksheet->tracking_transit,['class' => 'form-control'])!!}

							{!! Form::hidden('pallet_number',$new_worksheet->pallet_number,['class' => 'form-control'])!!}

							{!! Form::hidden('comment_2',$new_worksheet->comment_2,['class' => 'form-control'])!!}

							{!! Form::hidden('comments',$new_worksheet->comments,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_name',$new_worksheet->sender_name,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_country',$new_worksheet->sender_country,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_city',$new_worksheet->sender_city,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_postcode',$new_worksheet->sender_postcode,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_address',$new_worksheet->sender_address,['class' => 'form-control'])!!}

							{!! Form::hidden('standard_phone',$new_worksheet->standard_phone)!!}

							{!! Form::hidden('sender_phone',$new_worksheet->sender_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_passport',$new_worksheet->sender_passport,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_name',$new_worksheet->recipient_name,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_country',$new_worksheet->recipient_country,['class' => 'form-control'])!!}

							{!! Form::hidden('region',$new_worksheet->region)!!}

							{!! Form::hidden('district',$new_worksheet->district)!!}

							{!! Form::hidden('recipient_city',$new_worksheet->recipient_city,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_postcode',$new_worksheet->recipient_postcode,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_street',$new_worksheet->recipient_street,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_house',$new_worksheet->recipient_house,['class' => 'form-control'])!!}

							{!! Form::hidden('body',$new_worksheet->body)!!}

							{!! Form::hidden('recipient_room',$new_worksheet->recipient_room,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_phone',$new_worksheet->recipient_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_passport',$new_worksheet->recipient_passport,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_email',$new_worksheet->recipient_email,['class' => 'form-control'])!!}

							{!! Form::hidden('package_content',$new_worksheet->package_content,['class' => 'form-control'])!!}

							{!! Form::hidden('package_cost',$new_worksheet->package_cost,['class' => 'form-control'])!!}

							{!! Form::hidden('courier',$new_worksheet->courier,['class' => 'form-control'])!!}

							{!! Form::hidden('pick_up_date',$new_worksheet->pick_up_date,['class' => 'form-control'])!!}

							{!! Form::hidden('weight',$new_worksheet->weight,['class' => 'form-control'])!!}

							{!! Form::hidden('width',$new_worksheet->width,['class' => 'form-control'])!!}

							{!! Form::hidden('height',$new_worksheet->height,['class' => 'form-control'])!!}

							{!! Form::hidden('length',$new_worksheet->length,['class' => 'form-control'])!!}

							{!! Form::hidden('volume_weight',$new_worksheet->volume_weight,['class' => 'form-control'])!!}

							{!! Form::hidden('quantity_things',$new_worksheet->quantity_things,['class' => 'form-control'])!!}

							{!! Form::hidden('batch_number',$new_worksheet->batch_number,['class' => 'form-control'])!!}

							{!! Form::hidden('pay_date',$new_worksheet->pay_date,['class' => 'form-control'])!!}

							{!! Form::hidden('pay_sum',$new_worksheet->pay_sum,['class' => 'form-control'])!!}

							{!! Form::hidden('status_en',$new_worksheet->status_en,['class' => 'form-control'])!!}

							{!! Form::hidden('status_he',$new_worksheet->status_he,['class' => 'form-control'])!!}

							{!! Form::hidden('status_ua',$new_worksheet->status_ua,['class' => 'form-control'])!!}

							@if($new_column_1)
							{!! Form::hidden('new_column_1',$new_worksheet->new_column_1,['class' => 'form-control'])!!}
							@endif

							@if($new_column_2)
							{!! Form::hidden('new_column_2',$new_worksheet->new_column_2,['class' => 'form-control'])!!}
							@endif

							@if($new_column_3)
							{!! Form::hidden('new_column_3',$new_worksheet->new_column_3,['class' => 'form-control'])!!}
							@endif

							@if($new_column_4)
							{!! Form::hidden('new_column_4',$new_worksheet->new_column_4,['class' => 'form-control'])!!}
							@endif

							@if($new_column_5)
							{!! Form::hidden('new_column_5',$new_worksheet->new_column_5,['class' => 'form-control'])!!}
							@endif

							{!! Form::hidden('recipient_name_customs',$new_worksheet->recipient_name_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_country_customs',$new_worksheet->recipient_country_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_city_customs',$new_worksheet->recipient_city_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_postcode_customs',$new_worksheet->recipient_postcode_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_street_customs',$new_worksheet->recipient_street_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_house_customs',$new_worksheet->recipient_house_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_room_customs',$new_worksheet->recipient_room_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_phone_customs',$new_worksheet->recipient_phone_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_passport_customs',$new_worksheet->recipient_passport_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('update_status_date',$new_worksheet->update_status_date)!!}							
					
						{!! Form::button('Сохранить',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

						@endif
					
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