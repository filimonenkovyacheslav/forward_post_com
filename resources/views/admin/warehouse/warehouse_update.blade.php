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
					<a class="btn" id="add-tracking" data-toggle="modal" data-target="#warehouseAddTracking"></a>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>TRACKING NO.</th>
										<th>CHANGE</th>
										<th>NOTIFICATIONS</th>
									</tr>
								</thead>
								<tbody>

									@if($track_arr)
									@for($i=0; $i < count($track_arr); $i++)

									<tr>
										<td title="{{$track_arr[$i]}}">
											<div style="width: 150px">{{$track_arr[$i]}}</div>
										</td>
										<td class="td-button">
											{!! Form::open(['url'=>route('deleteTrackingFromPallet'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$track_arr[$i]) !!}
											{!! Form::hidden('pallet',$warehouse->pallet) !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											<a class="btn btn-success" onclick="runTrackingModal(event)" href="{{ url('/admin/warehouse-tracking-move/'.$track_arr[$i]) }}">Move</a>
										</td>
										@php
										$notifications = '';
										if($warehouse->notifications){
											$temp = json_decode($warehouse->notifications);
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
											<div style="width: 700px">{{$notifications}}</div>
										</td>
                 
									</tr>

									@endfor
									@endif
								</tbody>
							</table>
						
						<a class="btn btn-primary" onclick="addTrackingModal(event)" href="{{ url('/admin/warehouse-add-tracking/'.$id) }}">Add tracking</a>
						
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
					<label>New pallet No.:
						<select name="pallet"></select>
					</label>												
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>				
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="warehouseAddTracking" tabindex="-1" role="dialog" aria-labelledby="warehouseAddTrackingLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="warehouseAddTrackingLabel">Add tracking</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<label>New tracking:
						<input type="text" name="tracking">
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

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}


	var thisUrl = "{{ url('/') }}";


	function runTrackingModal(event)
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
				if (data.tracking) $('#warehouseRunModal form').attr('action',thisUrl+'/admin/warehouse-tracking-move/'+data.tracking);
				if (data.palletArr) {
					const palletArr = JSON.parse(data.palletArr);
					let html = '<option value=""></option>';
					for (let i = palletArr.length - 1; i >= 0; i--) {
						html += '<option value="'+palletArr[i]+'">'+palletArr[i]+'</option>';
					}
					$('#warehouseRunModal form select').html(html);
				}				
			},
			error: function (msg) {
				alert('Ошибка admin');
			}
		});		
	}


	function addTrackingModal(event)
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
				document.querySelector('#add-tracking').click();
				if (data.id) $('#warehouseAddTracking form').attr('action',thisUrl+'/admin/warehouse-add-tracking/'+data.id);
			},
			error: function (msg) {
				alert('Ошибка admin');
			}
		});		
	}

</script>
@else
<h1>You cannot view this page!</h1>
@endcan
@endsection