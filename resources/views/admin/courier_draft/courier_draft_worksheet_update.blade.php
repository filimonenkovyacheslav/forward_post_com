@extends('layouts.admin')
@section('content')

@can('editDraft')
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

					@if(isset($courier_draft_worksheet))				

						{!! Form::open(['url'=>route('courierDraftWorksheetUpdate', ['id'=>$courier_draft_worksheet->id]), 'class'=>'form-horizontal worksheet-update-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('site_name','Сайт',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('site_name', array('DD-C' => 'DD-C', 'For' => 'For'), $courier_draft_worksheet->site_name,['class' => 'form-control']) !!}
							</div>
						</div>

						@can('update-user')
						@php
							$courier_draft_worksheet->date = str_replace(".", "-", $courier_draft_worksheet->date);
						@endphp
						<div class="form-group">
							{!! Form::label('date','Дата',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::date('date',$courier_draft_worksheet->date,['class' => 'form-control'])!!}
							</div>
						</div>
						@endcan

						@if (!$courier_draft_worksheet->getLastDocUniq())
						
						<div class="form-group">
							{!! Form::label('tariff','Тариф',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('tariff', array('' => '', 'Обычный' => 'Обычный', 'Экспресс' => 'Экспресс'), $courier_draft_worksheet->tariff,['class' => 'form-control']) !!}
							</div>
						</div>
						
						@endif
						
						<div class="form-group">
							{!! Form::label('status','Статус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Доставляется на склад в стране отправителя' => 'Доставляется на склад в стране отправителя', 'На складе в стране отправителя' => 'На складе в стране отправителя', 'На таможне в стране отправителя' => 'На таможне в стране отправителя', 'Доставляется в страну получателя' => 'Доставляется в страну получателя', 'На таможне в стране получателя' => 'На таможне в стране получателя', 'Доставляется получателю' => 'Доставляется получателю', 'Доставлено' => 'Доставлено', 'Возврат' => 'Возврат', 'Коробка' => 'Коробка', 'Забрать' => 'Забрать', 'Уточнить' => 'Уточнить', 'Думают' => 'Думают', 'Отмена' => 'Отмена'), $courier_draft_worksheet->status,['class' => 'form-control']) !!}
							</div>
						</div>
						
						@can('update-user')
						<div class="form-group">
							{!! Form::label('status_date','Дата статуса',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::date('status_date',$courier_draft_worksheet->status_date,['class' => 'form-control'])!!}
							</div>
						</div>
						@endcan

						@can('update-user')
						<div class="form-group">
							{!! Form::label('order_date','Дата Заказа',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::date('order_date',$courier_draft_worksheet->order_date,['class' => 'form-control'])!!}
							</div>
						</div>
						@endcan
						
						<div class="form-group">
							{!! Form::label('partner','Партнер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('partner', array('' => '', 'viewer_1' => 'viewer_1', 'viewer_2' => 'viewer_2', 'viewer_3' => 'viewer_3', 'viewer_4' => 'viewer_4', 'viewer_5' => 'viewer_5'), $courier_draft_worksheet->partner,['class' => 'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('tracking_main','Трекинг Основной',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',$courier_draft_worksheet->tracking_main,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('parcels_qty','Кол-во посылок',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('parcels_qty',$courier_draft_worksheet->parcels_qty,['class' => 'form-control', 'required'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_local','Трекинг Локальные',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_local',$courier_draft_worksheet->tracking_local,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_transit','Трекинг Транзитные',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_transit',$courier_draft_worksheet->tracking_transit,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('pallet_number','№ паллеты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pallet_number',$courier_draft_worksheet->pallet_number,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comment_2','OFF Коммент',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comment_2',$courier_draft_worksheet->comment_2,['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('comments','DIR Комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments',$courier_draft_worksheet->comments,['class' => 'form-control'])!!}
							</div>
						</div>

						@if (!$courier_draft_worksheet->getLastDocUniq())
						
						<div class="form-group">
							{!! Form::label('sender_name','Отправитель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_name',$courier_draft_worksheet->sender_name,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_country','Страна отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('sender_country', array('Israel' => 'Israel', 'Germany' => 'Germany'), isset($courier_draft_worksheet->sender_country) ? $courier_draft_worksheet->sender_country : '',['class' => 'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('sender_city','Город отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-4">
								{!! Form::select('choose_city_ru', ['0' => 'Метод изменения города', '1' => 'Выбрать из списка (автоматически определится Регион)', '2' => 'Ввести вручную (Регион возможно не определится)'],'0',['class' => 'form-control']) !!}
							</div>
							<div class="col-md-4 choose-city-ru">
								@if (in_array($courier_draft_worksheet->sender_city, array_keys($israel_cities)))
								
								{!! Form::select('sender_city', $israel_cities, isset($courier_draft_worksheet->sender_city) ? $courier_draft_worksheet->sender_city : '',['class' => 'form-control']) !!}

								{!! Form::text('sender_city',$courier_draft_worksheet->sender_city,['class' => 'form-control','style' => 'display:none','disabled' => 'disabled'])!!}
								
								@else

								{!! Form::select('sender_city', $israel_cities, isset($courier_draft_worksheet->sender_city) ? $courier_draft_worksheet->sender_city : '',['class' => 'form-control','style' => 'display:none','disabled' => 'disabled']) !!}

								{!! Form::text('sender_city',$courier_draft_worksheet->sender_city,['class' => 'form-control'])!!}

								@endif

							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_postcode','Индекс отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_postcode',$courier_draft_worksheet->sender_postcode,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_address','Адрес отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_address',$courier_draft_worksheet->sender_address,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('standard_phone','Телефон отправителя (стандарт)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('standard_phone',$courier_draft_worksheet->standard_phone,['class' => 'form-control standard-phone'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_phone','Телефон отправителя (дополнительно)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_phone',$courier_draft_worksheet->sender_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_passport','Номер паспорта отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_passport',$courier_draft_worksheet->sender_passport,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_name','Получатель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_name',$courier_draft_worksheet->recipient_name,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_country','Страна получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
		                        {!! Form::select('recipient_country', array('RU' => 'Россия (RU)', 'UA' => 'Украина (UA)', 'BY' => 'Беларусь (BY)', 'KZ' => 'Казахстан (KZ)'), $courier_draft_worksheet->recipient_country,['class' => 'form-control']) !!}
		                    </div>
						</div>
						<div class="form-group">
							{!! Form::label('region','Регион',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('region',$courier_draft_worksheet->region,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('district','Район',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('district',$courier_draft_worksheet->district,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_city','Город получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_city',$courier_draft_worksheet->recipient_city,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_postcode','Индекс получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_postcode',$courier_draft_worksheet->recipient_postcode,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_street','Улица получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_street',$courier_draft_worksheet->recipient_street,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_house','Номер дома получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_house',$courier_draft_worksheet->recipient_house,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('body','корпус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('body',$courier_draft_worksheet->body,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_room','Номер квартиры получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_room',$courier_draft_worksheet->recipient_room,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_phone','Телефон получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_phone',$courier_draft_worksheet->recipient_phone,['class' => 'form-control'])!!}
							</div>
						</div>

						@endif
						
						<div class="form-group">
							{!! Form::label('recipient_passport','Номер паспорта получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_passport',$courier_draft_worksheet->recipient_passport,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_email','E-mail получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_email',$courier_draft_worksheet->recipient_email,['class' => 'form-control'])!!}
							</div>
						</div>

						@if (!$courier_draft_worksheet->getLastDocUniq())
						
						<div class="form-group">
							{!! Form::label('package_content','Содержимое посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('package_content',$courier_draft_worksheet->package_content,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('package_cost','Декларируемая стоимость посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('package_cost',$courier_draft_worksheet->package_cost,['class' => 'form-control'])!!}
							</div>
						</div>

						@endif
						
						<div class="form-group">
							{!! Form::label('courier','Курьер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('courier',json_decode($couriers_arr),$courier_draft_worksheet->courier,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('pick_up_date','Дата забора и комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pick_up_date',$courier_draft_worksheet->pick_up_date,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('weight','Вес посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',$courier_draft_worksheet->weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('width','Ширина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',$courier_draft_worksheet->width,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('height','Высота',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',$courier_draft_worksheet->height,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('length','Длина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',$courier_draft_worksheet->length,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('volume_weight','Объемный вес',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',$courier_draft_worksheet->volume_weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('quantity_things','Кол-во предметов',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('quantity_things',$courier_draft_worksheet->quantity_things,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('batch_number','Партия',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('batch_number',$courier_draft_worksheet->batch_number,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('pay_date','Дата оплаты и комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pay_date',$courier_draft_worksheet->pay_date,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pay_sum','Сумма оплаты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pay_sum',$courier_draft_worksheet->pay_sum,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_en_disabled','Статус (ENG)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_en_disabled',$courier_draft_worksheet->status_en,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_en',$courier_draft_worksheet->status_en,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_he_disabled','Статус (HE)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',$courier_draft_worksheet->status_he,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',$courier_draft_worksheet->status_he,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_ua_disabled','Статус (UA)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ua_disabled',$courier_draft_worksheet->status_ua,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ua',$courier_draft_worksheet->status_ua,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_name_customs','Получатель (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_name_customs',$courier_draft_worksheet->recipient_name_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_country_customs','Страна получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_country_customs',$courier_draft_worksheet->recipient_country_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_city_customs','Город получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_city_customs',$courier_draft_worksheet->recipient_city_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_postcode_customs','Индекс получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_postcode_customs',$courier_draft_worksheet->recipient_postcode_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_street_customs','Улица получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_street_customs',$courier_draft_worksheet->recipient_street_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_house_customs','Номер дома получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_house_customs',$courier_draft_worksheet->recipient_house_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_room_customs','Номер квартиры получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_room_customs',$courier_draft_worksheet->recipient_room_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_phone_customs','Телефон получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_phone_customs',$courier_draft_worksheet->recipient_phone_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_passport_customs','Номер паспорта получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_passport_customs',$courier_draft_worksheet->recipient_passport_customs,['class' => 'form-control'])!!}
							</div>
						</div>

							{!! Form::hidden('id',$courier_draft_worksheet->id)!!}

							{!! Form::hidden('shipper_region',$courier_draft_worksheet->shipper_region)!!}

							{!! Form::hidden('in_trash',$courier_draft_worksheet->in_trash)!!}

							{!! Form::hidden('parcels_qty',$courier_draft_worksheet->parcels_qty)!!}

							{!! Form::hidden('site_name',$courier_draft_worksheet->site_name,['class' => 'form-control'])!!}

							{!! Form::hidden('date',$courier_draft_worksheet->date,['class' => 'form-control'])!!}

							{!! Form::hidden('direction',$courier_draft_worksheet->direction,['class' => 'form-control'])!!}

							{!! Form::hidden('tariff',$courier_draft_worksheet->tariff,['class' => 'form-control'])!!}

							{!! Form::hidden('status',$courier_draft_worksheet->status,['class' => 'form-control'])!!}

							{!! Form::hidden('status_date',$courier_draft_worksheet->status_date)!!}

							{!! Form::hidden('order_date',$courier_draft_worksheet->order_date)!!}

							{!! Form::hidden('partner',$courier_draft_worksheet->partner,['class' => 'form-control'])!!}

							{!! Form::hidden('tracking_main',$courier_draft_worksheet->tracking_main,['class' => 'form-control'])!!}

							{!! Form::hidden('order_number',$courier_draft_worksheet->order_number)!!}

							{!! Form::hidden('tracking_local',$courier_draft_worksheet->tracking_local,['class' => 'form-control'])!!}

							{!! Form::hidden('tracking_transit',$courier_draft_worksheet->tracking_transit,['class' => 'form-control'])!!}

							{!! Form::hidden('pallet_number',$courier_draft_worksheet->pallet_number,['class' => 'form-control'])!!}

							{!! Form::hidden('comment_2',$courier_draft_worksheet->comment_2,['class' => 'form-control'])!!}

							{!! Form::hidden('comments',$courier_draft_worksheet->comments,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_name',$courier_draft_worksheet->sender_name,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_country',$courier_draft_worksheet->sender_country,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_city',$courier_draft_worksheet->sender_city,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_postcode',$courier_draft_worksheet->sender_postcode,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_address',$courier_draft_worksheet->sender_address,['class' => 'form-control'])!!}

							{!! Form::hidden('standard_phone',$courier_draft_worksheet->standard_phone)!!}

							{!! Form::hidden('sender_phone',$courier_draft_worksheet->sender_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('sender_passport',$courier_draft_worksheet->sender_passport,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_name',$courier_draft_worksheet->recipient_name,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_country',$courier_draft_worksheet->recipient_country,['class' => 'form-control'])!!}

							{!! Form::hidden('region',$courier_draft_worksheet->region)!!}

							{!! Form::hidden('district',$courier_draft_worksheet->district)!!}

							{!! Form::hidden('recipient_city',$courier_draft_worksheet->recipient_city,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_postcode',$courier_draft_worksheet->recipient_postcode,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_street',$courier_draft_worksheet->recipient_street,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_house',$courier_draft_worksheet->recipient_house,['class' => 'form-control'])!!}

							{!! Form::hidden('body',$courier_draft_worksheet->body)!!}

							{!! Form::hidden('recipient_room',$courier_draft_worksheet->recipient_room,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_phone',$courier_draft_worksheet->recipient_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_passport',$courier_draft_worksheet->recipient_passport,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_email',$courier_draft_worksheet->recipient_email,['class' => 'form-control'])!!}

							{!! Form::hidden('package_content',$courier_draft_worksheet->package_content,['class' => 'form-control'])!!}

							{!! Form::hidden('package_cost',$courier_draft_worksheet->package_cost,['class' => 'form-control'])!!}

							{!! Form::hidden('courier',$courier_draft_worksheet->courier,['class' => 'form-control'])!!}

							{!! Form::hidden('pick_up_date',$courier_draft_worksheet->pick_up_date,['class' => 'form-control'])!!}

							{!! Form::hidden('weight',$courier_draft_worksheet->weight,['class' => 'form-control'])!!}

							{!! Form::hidden('width',$courier_draft_worksheet->width,['class' => 'form-control'])!!}

							{!! Form::hidden('height',$courier_draft_worksheet->height,['class' => 'form-control'])!!}

							{!! Form::hidden('length',$courier_draft_worksheet->length,['class' => 'form-control'])!!}

							{!! Form::hidden('volume_weight',$courier_draft_worksheet->volume_weight,['class' => 'form-control'])!!}

							{!! Form::hidden('quantity_things',$courier_draft_worksheet->quantity_things,['class' => 'form-control'])!!}

							{!! Form::hidden('batch_number',$courier_draft_worksheet->batch_number,['class' => 'form-control'])!!}

							{!! Form::hidden('pay_date',$courier_draft_worksheet->pay_date,['class' => 'form-control'])!!}

							{!! Form::hidden('pay_sum',$courier_draft_worksheet->pay_sum,['class' => 'form-control'])!!}

							{!! Form::hidden('status_en',$courier_draft_worksheet->status_en,['class' => 'form-control'])!!}

							{!! Form::hidden('status_he',$courier_draft_worksheet->status_he,['class' => 'form-control'])!!}

							{!! Form::hidden('status_ua',$courier_draft_worksheet->status_ua,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_name_customs',$courier_draft_worksheet->recipient_name_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_country_customs',$courier_draft_worksheet->recipient_country_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_city_customs',$courier_draft_worksheet->recipient_city_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_postcode_customs',$courier_draft_worksheet->recipient_postcode_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_street_customs',$courier_draft_worksheet->recipient_street_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_house_customs',$courier_draft_worksheet->recipient_house_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_room_customs',$courier_draft_worksheet->recipient_room_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_phone_customs',$courier_draft_worksheet->recipient_phone_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('recipient_passport_customs',$courier_draft_worksheet->recipient_passport_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('update_status_date',$courier_draft_worksheet->update_status_date)!!}							
					
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