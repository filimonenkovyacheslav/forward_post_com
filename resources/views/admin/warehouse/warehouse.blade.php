@extends('layouts.admin')
@section('content')
@can('editColumns-2')
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
				<a href="{{ route('exportExcelWarehouse') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Export to Excel</a>
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
					
					<a class="btn" id="run-modal" data-toggle="modal" data-target="#warehouseRunModal"></a>

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ url('/admin/warehouse-filter/') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="pallet">PALLET</option>
									<option value="cell">CELL</option>
									<option value="arrived">ARRIVED</option>
									<option value="left">LEFT</option>
									<option value="lot">LOT</option>			
									<option value="notifications">NOTIFICATIONS</option>
									<option value="tracking_numbers">TRACKING</option>              
								</select>
							</label>
							<label>Filter:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							<input type="hidden" name="hide_left">
							<button type="button" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Search</button>
						</form>
						
						<label style="margin-top: 30px;margin-left: 30px;">Hide left pallets
							<input type="checkbox" onclick="hideLeft(event)" class="hide_left" style="width:20px;height:20px;">
						</label>						
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>V</th>
										<th>Change</th>
										<th>PALLET
											<a class="btn btn-primary" target="_blank" href="{{ route('palletsShow') }}">Change</a>
										</th>
										<th>CELL</th>
										<th>ARRIVED</th>
										<th>LEFT</th>
										<th>LOT</th>
										<th>NOTIFICATIONS</th>
									</tr>

								</thead>
								<tbody>

									@if($warehouse_obj->count())
									@foreach($warehouse_obj as $row)

									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>
										<td class="td-button">
											<a class="btn btn-primary" href="{{ url('/admin/warehouse-open/'.$row->id) }}">Open</a>

											<a class="btn btn-success" onclick="runModal(event)" href="{{ url('/admin/warehouse-edit/'.$row->id) }}">Edit</a>

											@can('editPost')

											{!! Form::open(['url'=>route('deleteWarehouse'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan

										</td>
										<td title="{{$row->pallet}}">
											<div class="div-3">{{$row->pallet}}</div>
										</td>
										<td title="{{$row->cell}}">
											<div class="div-3">{{$row->cell}}</div>
										</td>
										<td title="{{$row->arrived}}">
											<div class="div-3">{{$row->arrived}}</div>
										</td>
										<td title="{{$row->left}}">
											<div class="div-3">{{$row->left}}</div>
										</td>
										<td title="{{$row->lot}}">
											<div class="div-3">{{$row->lot}}</div>
										</td>
										@php
										$notifications = '';
										if($row->notifications){
											$temp = json_decode($row->notifications);
											if ($temp->tracking){
												$temp_mess = json_decode($temp->tracking);
												$temp_mess = $temp_mess->message;
											}
											else{
												$temp_mess = '';
											}
											if ($temp->pallet){
												$temp_pallet = json_decode($temp->pallet);
												if (is_object($temp_pallet)) {
													$temp_pallet = $temp_pallet->other_arr.' '.$temp_pallet->empty_arr;
												}
												else{
													$temp_pallet = $temp->pallet;
												}
											}
											else{
												$temp_pallet = '';
											}
											$notifications = $temp_pallet.' '.$temp_mess;
										}										
										@endphp										
										<td title="{{$notifications}}">
											<div style="width: 400px">{{$notifications}}</div>
										</td>                  
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $warehouse_obj->appends($data)->links() }}
							@else
							{{ $warehouse_obj->links() }}
							@endif

							<div class="checkbox-operations">
								
								{!! Form::open(['url'=>route('addWarehouseDataById'), 'class'=>'worksheet-add-form','method' => 'POST']) !!}

								<label>Select action with selected rows:
									<select class="form-control" name="checkbox_operations_select">
										<option value=""></option>
										@can('editPost')
										<option value="delete">Delete</option>
										@endcan
										<option value="change">Change</option>
									</select>
								</label>

								<label class="checkbox-operations-change">Choose column:
									<select class="form-control" id="warehouse-columns" name="warehouse-columns">
										<option value="" selected="selected"></option>
										<option value="cell">CELL</option>  
									</select>
								</label>	

								<label class="value-by-tracking checkbox-operations-change">Input value:
									<input class="form-control" type="text" name="value-by-id">
								</label>

								{!! Form::button('Save',['class'=>'btn btn-primary checkbox-operations-change','type'=>'submit']) !!}
								{!! Form::close() !!}

								@can('editPost')

								{!! Form::open(['url'=>route('deleteWarehouseById'),'onsubmit' => 'return ConfirmDelete()','method' => 'POST']) !!}
								{!! Form::button('Delete',['class'=>'btn btn-danger  checkbox-operations-delete','type'=>'submit']) !!}
								{!! Form::close() !!}

								@endcan

							</div>

							<div class="container">
								<div class="row">
									<div class="col-md-6">
										<label>IN - <span>{{$in_count}}</span> pallets</label><br>
										<label>CD - <span>{{$cd_count}}</span> pallets</label><br>
									</div>
									<div class="pallets-sum col-md-6">
										<h5>Pallets sum by date</h5>
										<br>
										<label>From:
											<input type="date" name="from_date">
										</label>
										<label>Till:
											<input type="date" name="to_date">
										</label>
										<button onclick="sumByDate()" class="btn btn-success">Sum</button>
									</div>
								</div>
							</div>							
													
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<!-- Modal -->
<div class="modal fade" id="warehouseRunModal" tabindex="-1" role="dialog" aria-labelledby="warehouseRunModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="warehouseRunModalLabel">Editing of <span class="pallet-title"></span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<label>CELL:
						<input type="text" name="cell" class="form-control form-control-sm">
					</label>
					<label>NOTIFICATIONS:
						<p class="form-control form-control-sm notifications"></p>
					</label>												
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>				
			</form>
		</div>
	</div>
</div>

<script>

	let href = location.href;
	if (href.indexOf('hide_left') !== -1) {
		document.querySelector('.hide_left').checked = true;
		document.querySelector('[name="hide_left"]').value = 'hide_left';
	}

	function hideLeft(event)
	{
		let href = location.href;
		
		if (event.target.checked){						
			document.querySelector('[name="hide_left"]').value = 'hide_left';
			location.href = addGet(href, 'hide_left=hide_left');
		}
		else{
			document.querySelector('[name="hide_left"]').value = '';
			location.href = removeURLParameter(href, 'hide_left');			
		}
	}

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

	function runModal(event)
	{
		event.preventDefault();
		const href = event.target.href;
		
		$.ajax({
			url: href,
			type: "GET",
			headers: {
				'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			},
			success: function (data) {
				document.querySelector('#run-modal').click();
				if (data.title) $('#warehouseRunModal .pallet-title').text(data.title);
				if (data.warehouse) {
					const warehouse = JSON.parse(data.warehouse);
					$('#warehouseRunModal form').attr('action','/admin/warehouse-edit/'+warehouse.id);
					$('#warehouseRunModal [name="cell"]').val(warehouse.cell);
					if (warehouse.notifications) {
						const notifications = JSON.parse(warehouse.notifications);
						const pallet = JSON.parse(notifications.pallet);
						$('#warehouseRunModal .notifications').text(pallet.empty_arr+' '+pallet.other_arr+' '+notifications.tracking);
					}					
				}					
			},
			error: function (msg) {
				alert('Ошибка admin');
			}
		});		
	}


	function sumByDate(){		
		let fromDate = document.querySelector('[name="from_date"]').value;
		let toDate = document.querySelector('[name="to_date"]').value;

		if (fromDate && toDate) {						
			$.ajax({
				url: "{{ url('/admin/pallets-sum/') }}"+"?from_date="+fromDate+"&to_date="+toDate,
				type: "GET",
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (data) {
					console.log(data)
					$('.pallets-sum .alert').remove()
					data = JSON.parse(data)
					if (data.error) {
						$('.pallets-sum button').after('<div class="alert alert-danger">'+data.error+'</div>')
					}
					if (data.sum) {
						$('.pallets-sum button').after('<div class="alert alert-success">Sum: '+data.sum+'</div>')
					}
				},
				error: function (msg) {
					alert('Error admin');
				}
			});
		}
	}

</script>
@else
<h1>You cannot view this page!</h1>
@endcan
@endsection