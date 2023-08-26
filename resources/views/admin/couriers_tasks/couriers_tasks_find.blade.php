@extends('layouts.phil_ind_admin')
@section('content')
@can('editCourierTasks')
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
				<a style="margin-bottom: 20px;" onclick="ConfirmExport(event)" class="btn btn-success btn-move">To Excel</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>

					@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@php
						session(['this_previous_url' => url()->full()]);
					@endphp					

					<div class="btn-move-wrapper" style="display:flex">

						<form action="{{ route('exportExcelCourierTask') }}" method="GET" id="form-export-field" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите регион/Choose region:
								<select class="form-control" id="export_region" name="export_region">
									<option value="" selected="selected"></option>
									<option value="North">North</option>
									<option value="South">South</option>
									<option value="Center">Center</option>
									<option value="Haifa">Haifa</option>
									<option value="Tel Aviv">Tel Aviv</option> 
									<option value="Jerusalem">Jerusalem</option>
									<option value="Eilat">Eilat</option>	
								</select>
							</label>
							<label class="table_columns" style="margin: 0 15px">Выберите сайт/Choose site:
								<select class="form-control" id="export_site" name="export_site">
									<option value="" selected="selected"></option>
									<option value="DD-C">DD-C</option>
									<option value="For">For</option>
									<option value="ORE">ORE</option>
								</select>
							</label>
						</form>
						
						<form action="{{ route('courierTaskFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку/Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="direction">Направление/Direction</option>
									<option value="site_name">Сайт/Site</option>
									<option value="status">Статус/Status</option>
									<option value="packing_num">Packing No.</option>
									<option value="comments_1">Комментарии 1/Comments 1</option>
									<option value="comments_2">Комментарии 2/Comments 2</option> 
									<option value="shipper_name">Отправитель/Shipper name</option>
									<option value="shipper_country">Страна отправителя/Shipper country</option>
									<option value="shipper_region">Регион отправителя/Shipper region</option>
									<option value="shipper_city">Город отправителя/Shipper city</option>
									<option value="shipper_address">Адрес отправителя/Shipper address</option>
									<option value="standard_phone">Телефон отправителя/Shipper phone</option>
									<option value="courier">Курьер/Courier</option>
									<option value="pick_up_date_comments">Дата забора/Pick up date</option>    
									<option value="weight">Вес/Weight</option>
									<option value="shipped_items">Содержание/Shipped items</option>
								</select>
							</label>
							<label>Фильтр/Filter:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							<button type="submit" id="table_filter_button" style="margin-left:35px" class="btn btn-default">Искать/Search</button>
						</form>
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>V</th>
										<th>Action</th>
										<th>№</th>
										<th>Dir-on</th>
										<th>Site</th>
										<th>Статус / Status</th>
										<th>Qty</th>
										<th>O/No</th>
										<th>Packing No.</th>
										<th>Комментарии 1 / Comments 1</th>
										<th>Комментарии 2 / Comments 2</th>
										<th>Отправитель / Shipper name</th> 
										<th>Country</th>
										<th>Region</th>
										<th>Город отправителя / Shipper city</th>
										<th>Адрес отправителя / Shipper address</th>
										<th>Телефон отправителя / Shipper phone</th>
										<th>Курьер / Courier</th>
										<th>Дата забора / Pick up date</th>
										<th>Вес / Weight</th>
										<th>Содержание / Shipped items</th>
									</tr>
								</thead>
								<tbody>

									@php
									$id_arr = [];
									@endphp

									@if(isset($filter_arr))
									@for($i=0; $i < count($filter_arr); $i++)
									@foreach($filter_arr[$i] as $row)

									@if (!in_array($row->id, $id_arr))
									@php
									$id_arr[] = $row->id;
									@endphp

									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>
										<td class="td-button">

											<a class="btn btn-success" data-id="{{ $row->id }}" onclick="ConfirmDone(event)" href="{{ url('/admin/couriers-tasks-done/'.$row->id) }}">Done</a>
											
										</td>
										<td title="{{$row->id}}">
											<div class="div-number">{{$row->id}}</div>
										</td>
										<td title="{{$row->direction}}">
											<div style="width: 50px">{{$row->direction}}</div>
										</td>
										<td title="{{$row->site_name}}">
											<div class="div-22">{{$row->site_name}}</div>
										</td>
										<td title="{{$row->status}}">
											<div class="div-3">{{$row->status}}</div>
										</td>
										<td title="{{$row->parcels_qty}}">
											<div class="div-number">{{$row->parcels_qty}}</div>
										</td>
										<td title="{{$row->order_number}}">
											<div class="div-number">{{$row->order_number}}</div>
										</td>
										<td title="{{$row->packing_num}}">
											<div class="div-3">{{$row->packing_num}}</div>
										</td>
										<td title="{{$row->comments_1}}">
											<div class="div-3">{{$row->comments_1}}</div>
										</td>
										<td title="{{$row->comments_2}}">
											<div class="div-3">{{$row->comments_2}}</div>
										</td>
										<td title="{{$row->shipper_name}}">
											<div class="div-3">{{$row->shipper_name}}</div>
										</td>
										<td title="{{$row->shipper_country}}">
											<div class="div-2">{{$row->shipper_country}}</div>
										</td>
										<td title="{{$row->shipper_region}}">
											<div class="div-2">{{$row->shipper_region}}</div>
										</td>
										<td title="{{$row->shipper_city}}">
											<div class="div-3">{{$row->shipper_city}}</div>
										</td>
										<td title="{{$row->shipper_address}}">
											<div class="div-invoice">{{$row->shipper_address}}</div>
										</td>
										<td title="{{$row->standard_phone}}">
											<div class="div-15">{{$row->standard_phone}}</div>
										</td>
										<td class="@can('editPost')allowed-update @endcan" title="{{$row->courier}}">
											<div data-name="courier" data-id="{{ $row->id }}" class="div-3">{{$row->courier}}</div>
										</td>
										<td class="@can('editPost')allowed-update @endcan" title="{{$row->pick_up_date_comments}}">
											<div data-name="pick_up_date_comments" data-id="{{ $row->id }}" class="div-3">{{$row->pick_up_date_comments}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-number">{{$row->weight}}</div>
										</td>
										<td title="{{$row->shipped_items}}">
											<div class="div-invoice">{{$row->shipped_items}}</div>
										</td>
									@endif
									@endforeach
									@endfor
									@endif
								</tbody>
							</table>

							<div class="checkbox-operations">

								{!! Form::open(['url'=>route('addCourierTaskDataById'), 'class'=>'worksheet-add-form','method' => 'POST']) !!}
																		
									<label style="display:none" class="checkbox-operations-change">
										<select class="form-control" id="tracking-columns" name="tracking-columns">
											<option value="" selected="selected"></option>
											<option value="direction">Направление/Direction</option>
											<option value="site_name">Сайт/Site</option>
											<option value="status">Статус/Status</option>
											<option value="comments_1">Комментарии 1/Comments 1</option>
											<option value="comments_2">Комментарии 2/Comments 2</option> 
											<option value="shipper_name">Отправитель/Shipper name</option>
											<option value="shipper_country">Страна отправителя/Shipper country</option>
											<option value="shipper_region">Регион отправителя/Shipper region</option>
											<option value="shipper_city">Город отправителя/Shipper city</option>
											<option value="shipper_address">Адрес отправителя/Shipper address</option>
											<option value="standard_phone">Телефон отправителя/Shipper phone</option>
											<option value="courier">Курьер/Courier</option>
											<option value="pick_up_date_comments">Дата забора/Pick up date</option>  
											<option value="weight">Вес/Weight</option>
											<option value="shipped_items">Содержание/Shipped items</option>
										</select>
									</label>	

									<label style="display:none" class="value-by-tracking checkbox-operations-change">
										<textarea class="form-control" name="value-by-tracking"></textarea>
									</label>									

								{!! Form::close() !!}

								{!! Form::open(['url'=>route('doneById'),'method' => 'POST','onsubmit' => 'ConfirmDone()']) !!}
								<a class="btn btn-success" onclick="ConfirmDone(event)" href="{{ url('/admin/couriers-tasks-done-id') }}">Done</a>
								{!! Form::close() !!}

							</div>
						
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
			<form action="{{ route('addCourierTaskDataById') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-group value-by-tracking">
						<textarea class="form-control" name="value-by-tracking"></textarea>
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

	function ConfirmDone(event)
	{
		event.preventDefault();
		const href = event.target.href;
		var x = confirm("Are you sure you want to mark as done?");
		if (x)
		{
			if (href.indexOf('couriers-tasks-done-id') !== -1)
			{
				if (!$('.checkbox-operations form [name="row_id[]"]').length)
					alert('Select rows!')
				else event.target.parentElement.submit();
			}
			else location.href = href;
		}			
		else
			return false;
	}
	

	function ConfirmExport(event)
	{
		const form = document.getElementById('form-export-field')
		const selectRegion = document.querySelector('select[name="export_region"]')
		const selectSite = document.querySelector('select[name="export_site"]')
		if ((selectRegion.value && selectSite.value) || (!selectRegion.value && !selectSite.value)) form.submit()
		else alert('Make choose!')
	}

</script>
@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection