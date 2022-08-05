@extends('layouts.admin')
@section('content')
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
				<a href="{{ route('exportExcelCourierDraft') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Экспорт в Excel</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>

					@if (session('status-error'))
					<div class="alert alert-danger">
						{{ session('status-error') }}
					</div>
					@elseif (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@php
						session(['this_previous_url' => url()->full()]);
					@endphp					

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ route('courierDraftWorksheetFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="id">Id</option>
									<option value="site_name">Сайт</option>
									<option value="packing_number">Packing No.</option>
									<option value="date">Дата</option>
									<option value="direction">Направление</option>
									<option value="tariff">Тариф</option>
									<option value="status">Статус</option>
									<option value="status_date">Дата Статуса</option>
									<option value="order_date">Дата Заказа</option>
									<option value="partner">Партнер</option>
									<option value="tracking_main">Основной</option>
									<option value="tracking_local">Локальный</option>
									<option value="tracking_transit">Транзитный</option>
									<option value="pallet_number">Номер паллеты</option>
									<option value="comment_2">Коммент</option>
									<option value="comments">Комментарии</option>
									<option value="sender_name">Отправитель</option>
									<option value="sender_country">Страна отправителя</option>
									<option value="shipper_region">Регион отправителя</option>
									<option value="sender_city">Город отправителя</option>
									<option value="sender_postcode">Индекс отправителя</option>
									<option value="sender_address">Адрес отправителя</option>
									<option value="standard_phone">Телефон (стандарт)</option>
									<option value="sender_phone">Телефон (дополнительно)</option>
									<option value="sender_passport">Номер паспорта отправителя</option>
									<option value="recipient_name">Получатель</option>
									<option value="recipient_country">Страна получателя</option>
									<option value="region">Регион</option>
									<option value="district">Район</option>
									<option value="recipient_city">Город получателя</option>
									<option value="recipient_postcode">Индекс получателя</option>
									<option value="recipient_street">Улица получателя</option>
									<option value="recipient_house">№ дома пол-ля</option>
									<option value="body">корпус</option>
									<option value="recipient_room">№ кв. пол-ля</option>
									<option value="recipient_phone">Телефон получателя</option>
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
									<option value="status_en">ENG Статус</option>
									<option value="status_he">HE Статус</option>
									<option value="status_ua">UA Статус</option>                  
								</select>
							</label>
							<label>Фильтр:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							
							<input type="hidden" name="for_active">
							
							<button type="button" id="table_filter_button" style="margin-left:35px" class="btn btn-default">Искать</button>
						</form>

						<label style="margin-top: 30px;margin-left: 30px;">Показывать только записи для активации
							<input type="checkbox" onclick="forActivation(event)" class="for_active" style="width:20px;height:20px;">
						</label>
						
					</div>

					<div class="checkbox-operations">

						@can('editDraft')
						
						{!! Form::open(['url'=>route('addCourierDraftDataById'), 'class'=>'worksheet-add-form','method' => 'POST']) !!}
						
						<label>Выберите действие с выбранными строчками:
							<select class="form-control" name="checkbox_operations_select">
								<option value=""></option>

								@can('editPost')	
								<option value="delete">Удалить</option>
								@endcan
								
								<option value="change">Изменить</option>
								<option value="double">Дубль</option>	

								@can('activateDraft')
								<option value="activate">Активировать</option>
								@endcan

								@can('editDraft')	
								<option value="cancel-pdf">Отменить PDF</option>
								<option value="add-pdf">Добавить PDF</option>
								<option value="download-pdf">Скачать PDF</option>
								@endcan

								@can('update-user')	
								<option value="admin-activate">Активировать(Admin)</option>
								@endcan

							</select>
						</label>
						
						<label class="checkbox-operations-change">Выберите колонку:
							<select class="form-control" id="tracking-columns" name="tracking-columns">
								<option value="" selected="selected"></option>
								<option value="site_name">Сайт</option>
								
								@can('update-user')
								<option value="date">Дата</option>
								@endcan
								
								<option value="direction">Направление</option>
								<option value="status">Статус</option>
								
								@can('update-user')
								<option value="status_date">Дата статуса</option>
								@endcan

								@can('update-user')
								<option value="order_date">Дата Заказа</option>
								@endcan
								
								<option value="partner">Партнер</option>
								<option value="parcels_qty">Кол-во посылок</option>
								<option value="tracking_local">Локальный</option>
								<option value="tracking_transit">Транзитный</option>
								<option value="pallet_number">№ паллеты</option>
								<option value="comment_2">Коммент</option>
								<option value="comments">Комментарии</option>
								<option value="sender_passport">Номер паспорта отправителя</option>
								<option value="recipient_passport">Номер паспорта получателя</option>
								<option value="recipient_email">E-mail получателя</option>
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

						<label class="value-by-tracking checkbox-operations-change">Введите значение:
							<textarea class="form-control" name="value-by-tracking"></textarea>
							<input type="hidden" name="status_en">
							<input type="hidden" name="status_ua">
							<input type="hidden" name="status_he">
						</label>									

						{!! Form::button('Сохранить',['class'=>'btn btn-primary checkbox-operations-change','type'=>'submit']) !!}
						{!! Form::close() !!}

						{!! Form::open(['url'=>route('deleteCourierDraftWorksheetById'),'method' => 'POST']) !!}
						{!! Form::button('Удалить',['class'=>'btn btn-danger  checkbox-operations-delete','type'=>'submit','onclick' => 'ConfirmDelete(event)']) !!}
						{!! Form::close() !!}

						<form class="checkbox-operations-change-one" action="{{ url('/admin/courier-draft-worksheet/') }}" method="GET">
							@csrf	
						</form>

						<form class="checkbox-operations-double" action="{{ url('/admin/courier-draft-worksheet-double/') }}" method="GET">
							@csrf	
							<input type="hidden" name="duplicate_qty" value="1">
						</form>

						<form class="checkbox-operations-activate" action="{{ url('/admin/courier-draft') }}" method="GET">
							@csrf	
						</form>

						<form class="checkbox-operations-admin-activate" action="{{ url('/admin/courier-draft-admin-activate') }}" method="GET">
							@csrf	
						</form>

						<form class="checkbox-operations-cancel-pdf" action="{{ route('cancelPdf') }}" method="POST">
							@csrf	
							<input type="hidden" name="draft_id" class="cancel-pdf">
						</form>

						<form class="checkbox-operations-add-pdf" action="{{ url('/form-with-signature') }}" method="GET">
							@csrf	
							<input type="hidden" name="quantity_sender" value="1">
						</form>

						<form class="checkbox-operations-download-pdf" action="{{ route('downloadAllPdf') }}" method="POST">
							@csrf	
							<input type="hidden" name="id" class="download-pdf">
							<input type="hidden" name="type" value="draft_id">
						</form>

						@endcan

					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>V</th>
										<th>Id</th>
										<th>№ пакинг-листа</th>
										<th>Сайт</th>
										<th>Дата</th>
										<th>Off<hr>Направ- ление</th>
										<th>Тариф</th>
										<th>Статус</th>
										<th>Дата Статуса</th>
										<th>Дата Заказа</th>
										<th>Партнер</th>
										<th>Трекинг<hr>Основной</th> 
										<th>№ заказа</th>
										<th>Кол-во посылок</th>
										<th>Трекинг<hr>Локальный</th>
										<th>Трекинг<hr>Транзитный</th>
										<th>Номер паллеты</th>
										<th>OFF<hr>Коммент</th>
										<th>DIR<hr>Комментарии</th>
										<th>Отправитель</th>
										<th>Страна отправителя</th>
										<th>Регион отправителя</th>
										<th>Город отправителя</th>
										<th>Индекс отправителя</th>
										<th>Адрес отправителя</th>
										<th>Телефон отправителя (стандарт)</th>
										<th>Телефон отправителя (дополнительно)</th>
										<th>Номер паспорта отправителя</th>
										<th>Получатель</th>
										<th>Страна получателя</th>
										<th>Регион</th>
										<th>Район</th>
										<th>Город получателя</th>
										<th>Индекс получателя</th>
										<th>Улица получателя</th>
										<th>№ дома пол-ля</th>
										<th>корпус</th>
										<th>№ кв. пол- ля</th>
										<th>Телефон получателя</th>
										<th>Номер паспорта получателя</th>
										<th>E-mail получателя</th>										
										<th>Стоимость посылки</th>
										<th>Курьер</th>
										<th>Дата забора и комментарии</th>
										<th>Вес посылки</th>
										<th>Ширина</th>
										<th>Высота</th>
										<th>Длина</th>
										<th>Объемный вес</th>
										<th>Кол-во предметов</th>
										<th>Партия</th>									
										<th>Дата оплаты и комментарии</th>
										<th>Сумма оплаты</th>
										<th>ENG<hr>Статус</th>
										<th>HE<hr>Статус</th>
										<th>UA<hr>Статус</th>					
										<th>Содержимое посылки</th>
										<th>Получатель (для таможни)</th>
										<th>Страна получателя (для таможни)</th>
										<th>Город получателя (для таможни)</th>
										<th>Индекс получателя (для таможни)</th>
										<th>Улица получателя (для таможни)</th>
										<th>№ дома пол-ля (для таможни)</th>
										<th>№ кв. пол- ля (для таможни)</th>
										<th>Телефон получателя (для таможни)</th>
										<th>Номер паспорта получателя (для таможни)</th>
									</tr>

								</thead>
								<tbody>

									@if(isset($courier_draft_worksheet_obj))
									@foreach($courier_draft_worksheet_obj as $row)

									@if(!in_array($user->role, $viewer_arr))

									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>		
										<td title="{{$row->id}}">
											<div class="div-22">{{$row->id}}</div>
										</td>	
										<td title="{{$row->getLastDocUniq()}}">
											<div class="div-3">{{$row->getLastDocUniq()}}</div>
										</td>							
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->site_name}}">
											<div data-name="site_name" data-id="{{ $row->id }}" class="div-22">{{$row->site_name}}</div>
										</td>
										<td class="@can('update-user')allowed-update @endcan" title="{{$row->date}}">
											<div data-name="date" data-id="{{ $row->id }}" class="div-3">{{$row->date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->direction}}">
											<div data-name="direction" data-id="{{ $row->id }}" class="div-2">{{$row->direction}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->tariff}}">
											<div data-name="tariff" data-id="{{ $row->id }}" class="div-2">{{$row->tariff}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status}}">
											<div data-name="status" data-id="{{ $row->id }}" class="div-3">{{$row->status}}</div>
										</td>
										<td class="@can('update-user')allowed-update @endcan" title="{{$row->status_date}}">
											<div data-name="status_date" data-id="{{ $row->id }}" class="div-3">{{$row->status_date}}</div>
										</td>
										<td class="@can('update-user')allowed-update @endcan" title="{{$row->order_date}}">
											<div data-name="order_date" data-id="{{ $row->id }}" class="div-3">{{$row->order_date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->partner}}">
											<div data-name="partner" data-id="{{ $row->id }}" class="div-3">{{$row->partner}}</div>
										</td>										
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->tracking_main}}">
											<div data-name="tracking_main" data-id="{{ $row->id }}" class="div-4">{{$row->tracking_main}}</div>
										</td>
										<td class="td-button" title="{{$row->order_number}}">
											<div class="div-22">{{$row->order_number}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->parcels_qty}}">
											<div data-name="parcels_qty" data-id="{{ $row->id }}" class="div-22">{{$row->parcels_qty}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->tracking_local}}">
											<div data-name="tracking_local" data-id="{{ $row->id }}" class="div-5">{{$row->tracking_local}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->tracking_transit}}">
											<div data-name="tracking_transit" data-id="{{ $row->id }}" class="div-6">{{$row->tracking_transit}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pallet_number}}">
											<div data-name="pallet_number" data-id="{{ $row->id }}" class="div-7">{{$row->pallet_number}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->comment_2}}">
											<div data-name="comment_2" data-id="{{ $row->id }}" class="div-8">{{$row->comment_2}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->comments}}">
											<div data-name="comments" data-id="{{ $row->id }}" class="div-9">{{$row->comments}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_name}}">
											<div data-name="sender_name" data-id="{{ $row->id }}" class="div-10">{{$row->sender_name}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_country}}">
											<div data-name="sender_country" data-id="{{ $row->id }}" class="div-11">{{$row->sender_country}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_region}}">
											<div class="div-2">{{$row->shipper_region}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_city}}">
											<div data-name="sender_city" data-id="{{ $row->id }}" class="div-12">{{$row->sender_city}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_postcode}}">
											<div data-name="sender_postcode" data-id="{{ $row->id }}" class="div-13">{{$row->sender_postcode}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_address}}">
											<div data-name="sender_address" data-id="{{ $row->id }}" class="div-14">{{$row->sender_address}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->standard_phone}}">
											<div data-name="standard_phone" data-id="{{ $row->id }}" class="div-15">{{$row->standard_phone}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_phone}}">
											<div data-name="sender_phone" data-id="{{ $row->id }}" class="div-15">{{$row->sender_phone}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->sender_passport}}">
											<div data-name="sender_passport" data-id="{{ $row->id }}" class="div-16">{{$row->sender_passport}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_name}}">
											<div data-name="recipient_name" data-id="{{ $row->id }}" class="div-17">{{$row->recipient_name}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_country}}">
											<div data-name="recipient_country" data-id="{{ $row->id }}" class="div-18">{{$row->recipient_country}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->region}}">
											<div data-name="region" data-id="{{ $row->id }}" class="div-18">{{$row->region}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->district}}">
											<div data-name="district" data-id="{{ $row->id }}" class="div-18">{{$row->district}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_city}}">
											<div data-name="recipient_city" data-id="{{ $row->id }}" class="div-19">{{$row->recipient_city}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_postcode}}">
											<div data-name="recipient_postcode" data-id="{{ $row->id }}" class="div-20">{{$row->recipient_postcode}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_street}}">
											<div data-name="recipient_street" data-id="{{ $row->id }}" class="div-21">{{$row->recipient_street}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_house}}">
											<div data-name="recipient_house" data-id="{{ $row->id }}" class="div-22">{{$row->recipient_house}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->body}}">
											<div data-name="body" data-id="{{ $row->id }}" class="div-22">{{$row->body}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_room}}">
											<div data-name="recipient_room" data-id="{{ $row->id }}" class="div-23">{{$row->recipient_room}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_phone}}">
											<div data-name="recipient_phone" data-id="{{ $row->id }}" class="div-24">{{$row->recipient_phone}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_passport}}">
											<div data-name="recipient_passport" data-id="{{ $row->id }}" class="div-25">{{$row->recipient_passport}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_email}}">
											<div data-name="recipient_email" data-id="{{ $row->id }}" class="div-26">{{$row->recipient_email}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->package_cost}}">
											<div data-name="package_cost" data-id="{{ $row->id }}" class="div-27">{{$row->package_cost}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->courier}}">
											<div data-name="courier" data-id="{{ $row->id }}" class="div-28">{{$row->courier}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pick_up_date}}">
											<div data-name="pick_up_date" data-id="{{ $row->id }}" class="div-29">{{$row->pick_up_date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->weight}}">
											<div data-name="weight" data-id="{{ $row->id }}" class="div-30">{{$row->weight}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->width}}">
											<div data-name="width" data-id="{{ $row->id }}" class="div-31">{{$row->width}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->height}}">
											<div data-name="height" data-id="{{ $row->id }}" class="div-32">{{$row->height}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->length}}">
											<div data-name="length" data-id="{{ $row->id }}" class="div-33">{{$row->length}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->volume_weight}}">
											<div data-name="volume_weight" data-id="{{ $row->id }}" class="div-34">{{$row->volume_weight}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->quantity_things}}">
											<div data-name="quantity_things" data-id="{{ $row->id }}" class="div-35">{{$row->quantity_things}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->batch_number}}">
											<div data-name="batch_number" data-id="{{ $row->id }}" class="div-36">{{$row->batch_number}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pay_date}}">
											<div data-name="pay_date" data-id="{{ $row->id }}" class="div-37">{{$row->pay_date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pay_sum}}">
											<div data-name="pay_sum" data-id="{{ $row->id }}" class="div-38">{{$row->pay_sum}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status_en}}">
											<div class="div-39">{{$row->status_en}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status_he}}">
											<div class="div-40">{{$row->status_he}}</div>
										</td> 
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status_ua}}">
											<div class="div-41">{{$row->status_ua}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->package_content}}">
											<div data-name="package_content" data-id="{{ $row->id }}" class="div1">{{$row->package_content}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_name_customs}}">
											<div data-name="recipient_name_customs" data-id="{{ $row->id }}" class="div-17">{{$row->recipient_name_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_country_customs}}">
											<div data-name="recipient_country_customs" data-id="{{ $row->id }}" class="div-18">{{$row->recipient_country_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_city_customs}}">
											<div data-name="recipient_city_customs" data-id="{{ $row->id }}" class="div-19">{{$row->recipient_city_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_postcode_customs}}">
											<div data-name="recipient_postcode_customs" data-id="{{ $row->id }}" class="div-20">{{$row->recipient_postcode_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_street_customs}}">
											<div data-name="recipient_street_customs" data-id="{{ $row->id }}" class="div-21">{{$row->recipient_street_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_house_customs}}">
											<div data-name="recipient_house_customs" data-id="{{ $row->id }}" class="div-22">{{$row->recipient_house_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_room_customs}}">
											<div data-name="recipient_room_customs" data-id="{{ $row->id }}" class="div-23">{{$row->recipient_room_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_phone_customs}}">
											<div data-name="recipient_phone_customs" data-id="{{ $row->id }}" class="div-24">{{$row->recipient_phone_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_passport_customs}}">
											<div data-name="recipient_passport_customs" data-id="{{ $row->id }}" class="div-25">{{$row->recipient_passport_customs}}</div>
										</td>                                                                  
									</tr>

									@elseif($row->partner === $user->role)

									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>										
										<td title="{{$row->id}}">
											<div class="div-22">{{$row->id}}</div>
										</td>
										<td title="{{$row->getLastDocUniq()}}">
											<div class="div-3">{{$row->getLastDocUniq()}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->site_name}}">
											<div data-name="site_name" data-id="{{ $row->id }}" class="div-22">{{$row->site_name}}</div>
										</td>
										<td class="@can('update-user')allowed-update @endcan" title="{{$row->date}}">
											<div data-name="date" data-id="{{ $row->id }}" class="div-3">{{$row->date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->direction}}">
											<div data-name="direction" data-id="{{ $row->id }}" class="div-2">{{$row->direction}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->tariff}}">
											<div data-name="tariff" data-id="{{ $row->id }}" class="div-2">{{$row->tariff}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status}}">
											<div data-name="status" data-id="{{ $row->id }}" class="div-3">{{$row->status}}</div>
										</td>
										<td class="@can('update-user')allowed-update @endcan" title="{{$row->status_date}}">
											<div data-name="status_date" data-id="{{ $row->id }}" class="div-3">{{$row->status_date}}</div>
										</td>
										<td class="@can('update-user')allowed-update @endcan" title="{{$row->order_date}}">
											<div data-name="order_date" data-id="{{ $row->id }}" class="div-3">{{$row->order_date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->partner}}">
											<div data-name="partner" data-id="{{ $row->id }}" class="div-3">{{$row->partner}}</div>
										</td>										
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->tracking_main}}">
											<div data-name="tracking_main" data-id="{{ $row->id }}" class="div-4">{{$row->tracking_main}}</div>
										</td>
										<td class="td-button" title="{{$row->order_number}}">
											<div class="div-22">{{$row->order_number}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->parcels_qty}}">
											<div data-name="parcels_qty" data-id="{{ $row->id }}" class="div-22">{{$row->parcels_qty}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->tracking_local}}">
											<div data-name="tracking_local" data-id="{{ $row->id }}" class="div-5">{{$row->tracking_local}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->tracking_transit}}">
											<div data-name="tracking_transit" data-id="{{ $row->id }}" class="div-6">{{$row->tracking_transit}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pallet_number}}">
											<div data-name="pallet_number" data-id="{{ $row->id }}" class="div-7">{{$row->pallet_number}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->comment_2}}">
											<div data-name="comment_2" data-id="{{ $row->id }}" class="div-8">{{$row->comment_2}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->comments}}">
											<div data-name="comments" data-id="{{ $row->id }}" class="div-9">{{$row->comments}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_name}}">
											<div data-name="sender_name" data-id="{{ $row->id }}" class="div-10">{{$row->sender_name}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_country}}">
											<div data-name="sender_country" data-id="{{ $row->id }}" class="div-11">{{$row->sender_country}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_region}}">
											<div class="div-2">{{$row->shipper_region}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_city}}">
											<div data-name="sender_city" data-id="{{ $row->id }}" class="div-12">{{$row->sender_city}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_postcode}}">
											<div data-name="sender_postcode" data-id="{{ $row->id }}" class="div-13">{{$row->sender_postcode}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_address}}">
											<div data-name="sender_address" data-id="{{ $row->id }}" class="div-14">{{$row->sender_address}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->standard_phone}}">
											<div data-name="standard_phone" data-id="{{ $row->id }}" class="div-15">{{$row->standard_phone}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->sender_phone}}">
											<div data-name="sender_phone" data-id="{{ $row->id }}" class="div-15">{{$row->sender_phone}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->sender_passport}}">
											<div data-name="sender_passport" data-id="{{ $row->id }}" class="div-16">{{$row->sender_passport}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_name}}">
											<div data-name="recipient_name" data-id="{{ $row->id }}" class="div-17">{{$row->recipient_name}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_country}}">
											<div data-name="recipient_country" data-id="{{ $row->id }}" class="div-18">{{$row->recipient_country}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->region}}">
											<div data-name="region" data-id="{{ $row->id }}" class="div-18">{{$row->region}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->district}}">
											<div data-name="district" data-id="{{ $row->id }}" class="div-18">{{$row->district}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_city}}">
											<div data-name="recipient_city" data-id="{{ $row->id }}" class="div-19">{{$row->recipient_city}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_postcode}}">
											<div data-name="recipient_postcode" data-id="{{ $row->id }}" class="div-20">{{$row->recipient_postcode}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_street}}">
											<div data-name="recipient_street" data-id="{{ $row->id }}" class="div-21">{{$row->recipient_street}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_house}}">
											<div data-name="recipient_house" data-id="{{ $row->id }}" class="div-22">{{$row->recipient_house}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->body}}">
											<div data-name="body" data-id="{{ $row->id }}" class="div-22">{{$row->body}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_room}}">
											<div data-name="recipient_room" data-id="{{ $row->id }}" class="div-23">{{$row->recipient_room}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->recipient_phone}}">
											<div data-name="recipient_phone" data-id="{{ $row->id }}" class="div-24">{{$row->recipient_phone}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_passport}}">
											<div data-name="recipient_passport" data-id="{{ $row->id }}" class="div-25">{{$row->recipient_passport}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_email}}">
											<div data-name="recipient_email" data-id="{{ $row->id }}" class="div-26">{{$row->recipient_email}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->package_cost}}">
											<div data-name="package_cost" data-id="{{ $row->id }}" class="div-27">{{$row->package_cost}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->courier}}">
											<div data-name="courier" data-id="{{ $row->id }}" class="div-28">{{$row->courier}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pick_up_date}}">
											<div data-name="pick_up_date" data-id="{{ $row->id }}" class="div-29">{{$row->pick_up_date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->weight}}">
											<div data-name="weight" data-id="{{ $row->id }}" class="div-30">{{$row->weight}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->width}}">
											<div data-name="width" data-id="{{ $row->id }}" class="div-31">{{$row->width}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->height}}">
											<div data-name="height" data-id="{{ $row->id }}" class="div-32">{{$row->height}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->length}}">
											<div data-name="length" data-id="{{ $row->id }}" class="div-33">{{$row->length}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->volume_weight}}">
											<div data-name="volume_weight" data-id="{{ $row->id }}" class="div-34">{{$row->volume_weight}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->quantity_things}}">
											<div data-name="quantity_things" data-id="{{ $row->id }}" class="div-35">{{$row->quantity_things}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->batch_number}}">
											<div data-name="batch_number" data-id="{{ $row->id }}" class="div-36">{{$row->batch_number}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pay_date}}">
											<div data-name="pay_date" data-id="{{ $row->id }}" class="div-37">{{$row->pay_date}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->pay_sum}}">
											<div data-name="pay_sum" data-id="{{ $row->id }}" class="div-38">{{$row->pay_sum}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status_en}}">
											<div class="div-39">{{$row->status_en}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status_he}}">
											<div class="div-40">{{$row->status_he}}</div>
										</td> 
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->status_ua}}">
											<div class="div-41">{{$row->status_ua}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->package_content}}">
											<div data-name="package_content" data-id="{{ $row->id }}" class="div1">{{$row->package_content}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_name_customs}}">
											<div data-name="recipient_name_customs" data-id="{{ $row->id }}" class="div-17">{{$row->recipient_name_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_country_customs}}">
											<div data-name="recipient_country_customs" data-id="{{ $row->id }}" class="div-18">{{$row->recipient_country_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_city_customs}}">
											<div data-name="recipient_city_customs" data-id="{{ $row->id }}" class="div-19">{{$row->recipient_city_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_postcode_customs}}">
											<div data-name="recipient_postcode_customs" data-id="{{ $row->id }}" class="div-20">{{$row->recipient_postcode_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_street_customs}}">
											<div data-name="recipient_street_customs" data-id="{{ $row->id }}" class="div-21">{{$row->recipient_street_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_house_customs}}">
											<div data-name="recipient_house_customs" data-id="{{ $row->id }}" class="div-22">{{$row->recipient_house_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_room_customs}}">
											<div data-name="recipient_room_customs" data-id="{{ $row->id }}" class="div-23">{{$row->recipient_room_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_phone_customs}}">
											<div data-name="recipient_phone_customs" data-id="{{ $row->id }}" class="div-24">{{$row->recipient_phone_customs}}</div>
										</td>
										<td class="@can('editDraft')allowed-update @endcan" title="{{$row->recipient_passport_customs}}">
											<div data-name="recipient_passport_customs" data-id="{{ $row->id }}" class="div-25">{{$row->recipient_passport_customs}}</div>
										</td>                                                                      
									</tr>

									@endif

									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $courier_draft_worksheet_obj->appends($data)->links() }}
							@else
							{{ $courier_draft_worksheet_obj->links() }}
							@endif
														

						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<!-- Modal -->
<a id="update-cell" data-toggle="modal" data-target="#updateCellModal"></a>

<div class="modal fade" id="updateCellModal" tabindex="-1" role="dialog" aria-labelledby="updateCellModalLabel" aria-hidden="true" style="background: rgba(0, 0, 0, 0.4);">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateCellModalLabel">Update cell</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('addCourierDraftDataById') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-group value-by-tracking">
						<textarea class="form-control" name="value-by-tracking"></textarea>
						<input type="hidden" name="status_en">
						<input type="hidden" name="status_ua">
						<input type="hidden" name="status_he">
					</div>					
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" style="font-size:.8rem">SAVE</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>	

	let href = location.href;
	if (href.indexOf('for_active') !== -1) {
		document.querySelector('.for_active').checked = true;
		document.querySelector('[name="for_active"]').value = 'for_active';
	}
	
	
	function ConfirmDelete(event)
	{
		let href = location.href;
		const newHref = href.split('/admin/')[0];
		event.preventDefault();
		const form = event.target.parentElement;
		const data = new URLSearchParams(new FormData(form)).toString();
		var x = confirm("Вы уверены, что хотите удалить окончательно?");
		if (x){
			$.ajax({
				url: newHref+'/admin/to-logs?'+data+'&table=courier_draft_worksheet',
				type: "GET",
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (data) {
					data = JSON.parse(data)
					if (data.status) {
						form.submit();
					}
				},
				error: function (msg) {
					alert('Error admin');
				}
			});				
		}
		else
			location.href = newHref+'/admin/to-trash?'+data+'&table=courier_draft_worksheet';
	}

</script>

@endsection