@extends('layouts.phil_ind_admin')
@section('content')
@can('editPost')
<!-- <div class="breadcrumbs">
	<div class="col-sm-4">
		<div class="page-header float-left">
			<div class="page-title">
				<h1>Control Panel</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="page-header float-right">
			<div class="page-title">
				<ol class="breadcrumb text-right">
					<li><a href="{{route('adminPhilIndIndex')}}">Control Panel</a></li>
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
				<a href="{{ route('exportExcelReceipts') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Export to Excel</a>
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
					
					<a class="btn btn-primary btn-move" id="add-rows" data-toggle="modal" data-target="#addRowsModal">Добавить строки (Add rows)</a>

					@can('update-user')
					
					<a class="btn btn-danger btn-move" id="delete-rows" data-toggle="modal" data-target="#deleteRowsModal">Delete rows</a>

					@endcan

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ url('/admin/receipts-filter/'.$legal_entity) }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку (Choose column):
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="legal_entity">Юр. лицо (Legal entity)</option>
									<option value="receipt_number">№ квитанции (Receipt number)</option>
									<option value="sum">Сумма (Sum)</option>
									<option value="date">Дата (Date)</option>
									<option value="tracking_main">Номер посылки (Tracking number)</option>			
									<option value="courier_name">Имя курьера (Courier name)</option>
									<option value="comment">Комментарий (Comment)</option>                
								</select>
							</label>
							<label>Фильтр (Filter):
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							<button type="button" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Поиск (Search)</button>
						</form>
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Изменить (Change)</th>
										<th>Юр. лицо (Legal entity)</th>
										<th>№ квитанции (Receipt number)</th>
										<th>Сумма (Sum)</th>
										<th>Дата (Date)</th>
										<th>Номер посылки (Tracking number)</th>
										<th>Имя курьера (Courier name)</th>
										<th>Комментарий (Comment)</th>
									</tr>

								</thead>
								<tbody>

									@if(isset($receipts_obj))
									@foreach($receipts_obj as $row)

									<tr>
										<td class="td-button">
											<a class="btn btn-primary" href="{{ url('/admin/receipts-update/'.$row->id) }}">Редактировать (Edit)</a>

											@if(!$row->double)

											<a class="btn btn-success" href="{{ url('/admin/receipts-double/'.$row->id) }}">Добавить дубль (Add)</a>

											@else

											{!! Form::open(['url'=>route('deleteReceipt'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Удалить дубль(Delete)',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endif

										</td>
										<td title="{{$row->legal_entity}}">
											<div class="div-3">{{$row->legal_entity}}</div>
										</td>
										<td title="{{$row->receipt_number}}">
											<div class="div-3">{{$row->receipt_number}}</div>
										</td>
										<td title="{{$row->sum}}">
											<div class="div-3">{{$row->sum}}</div>
										</td>
										<td title="{{$row->date}}">
											<div class="div-3">{{$row->date}}</div>
										</td>
										<td title="{{$row->tracking_main}}">
											<div class="div-3">{{$row->tracking_main}}</div>
										</td>										
										<td title="{{$row->courier_name}}">
											<div class="div-3">{{$row->courier_name}}</div>
										</td>
										<td title="{{$row->comment}}">
											<div class="div-3">{{$row->comment}}</div>
										</td>                  
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $receipts_obj->appends($data)->links() }}
							@else
							{{ $receipts_obj->links() }}
							@endif

							<div class="receipts-sum">
								<h5>Просуммировать все суммы оплат между датами «От и До» включительно</h5>
								<br>
								<label>От:
									<input type="date" name="from_date">
								</label>
								<label>До:
									<input type="date" name="to_date">
								</label>
								<button onclick="sumByDate()" class="btn btn-success">Просуммировать</button>
							</div>
						
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<!-- Modal -->
<div class="modal fade" id="addRowsModal" tabindex="-1" role="dialog" aria-labelledby="addRowsModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addRowsModalLabel">Добавить строки (Add rows)</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('receiptsAdd') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label">Выберите диапазон (Select a range)</label>
						<select class="form-control" name="range_select">
							<option value="ХХ01">ХХ01 - ХХ50</option>
							<option value="ХХ51">ХХ51 - ХХ00</option>							
						</select>
					</div>	
					<div class="form-group">
						<label class="control-label">Выберите Юр. лицо (Select a Legal entity)</label>
						<select class="form-control" name="legal_entity">
							<option value="UL">Юнион Логистик</option>
							<option value="DD">Д.Дымщиц</option>							
						</select>
					</div>													
					<div class="form-group">
						<label class="control-label">Начало диапазона (Range start)</label>
						<p>* Введите без двух последних цифр (Enter without the last two digits)</p>
						<input type="number" name="range_start" class="form-control" min="0">
					</div>
					<div class="form-group">
						<label class="control-label">Имя Курьера (Courier name)</label>
						<input type="text" name="courier_name" class="form-control">
					</div>													
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" style="font-size:.8rem">Добавить серию номеров квитанций (Add a series of receipt numbers)</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteRowsModal" tabindex="-1" role="dialog" aria-labelledby="deleteRowsModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="deleteRowsModalLabel">Удалить строки (Delete rows)</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('deleteReceipts') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label">Выберите диапазон (Select a range)</label>
						<select class="form-control" name="range_select">
							<option value="ХХ01">ХХ01 - ХХ50</option>
							<option value="ХХ51">ХХ51 - ХХ00</option>							
						</select>
					</div>	
					<div class="form-group">
						<label class="control-label">Выберите Юр. лицо (Select a Legal entity)</label>
						<select class="form-control" name="legal_entity">
							<option value="UL">Юнион Логистик</option>
							<option value="DD">Д.Дымщиц</option>							
						</select>
					</div>													
					<div class="form-group">
						<label class="control-label">Начало диапазона (Range start)</label>
						<p>* Введите без двух последних цифр (Enter without the last two digits)</p>
						<input type="number" name="range_start" class="form-control" min="0">
					</div>												
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" style="font-size:.8rem">Удалить серию номеров квитанций (Delete a series of receipt numbers)</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

	function sumByDate(){		
		let fromDate = document.querySelector('[name="from_date"]').value;
		let toDate = document.querySelector('[name="to_date"]').value;

		if (fromDate && toDate) {
			fromDate = fromDate.split('-');
			fromDate = fromDate[0].slice(2)+fromDate[1]+fromDate[2];
			toDate = toDate.split('-');
			toDate = toDate[0].slice(2)+toDate[1]+toDate[2];
			
			$.ajax({
				url: "{{ url('/admin/receipts-sum/'.$legal_entity) }}"+"?from_date="+fromDate+"&to_date="+toDate,
				type: "GET",
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (data) {
					$('.receipts-sum .alert').remove()
					data = JSON.parse(data)
					if (data.error) {
						$('.receipts-sum button').after('<div class="alert alert-danger">'+data.error+'</div>')
					}
					if (data.sum) {
						$('.receipts-sum button').after('<div class="alert alert-success">Сумма: '+data.sum+'</div>')
					}
				},
				error: function (msg) {
					alert('Ошибка admin');
				}
			});
		}
	}

</script>
@else
<h1>Вы не можете просматривать эту страницу (You cannot view this page)!</h1>
@endcan
@endsection