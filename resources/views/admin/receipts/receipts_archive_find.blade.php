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
						<form action="{{ route('receiptsArchiveFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку (Choose column):
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="receipt_number">№ квитанции (Receipt number)</option>
									<option value="tracking_main">Номер посылки (Tracking number)</option>			
									<option value="description">Описание (Description)</option>  
									<option value="comment">Комментарий (Comment)</option>             
								</select>
							</label>
							<label>Фильтр (Filter):
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							<button type="submit" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Поиск (Search)</button>
						</form>
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>№ квитанции (Receipt number)</th>
										<th>Номер посылки (Tracking number)</th>
										<th>Описание (Description)</th>
										<th>Комментарий (Comment)</th>
										<th>Изменить (Change)</th>
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
										<td title="{{$row->receipt_number}}">
											<div class="div-3">{{$row->receipt_number}}</div>
										</td>
										<td title="{{$row->tracking_main}}">
											<div style="width: 120px">{{$row->tracking_main}}</div>
										</td>										
										<td title="{{$row->description}}">
											<div style="width: 600px">{{$row->description}}</div>
										</td>
										<td title="{{$row->comment}}">
											<div style="width: 200px">{{$row->comment}}</div>
										</td> 		
										<td class="td-button"> 

											<a class="btn btn-primary" href="{{ url('/admin/receipts-archive-update/'.$row->id) }}">Редактировать</a>
										
										@can('update-user')
										
										{!! Form::open(['url'=>route('deleteReceiptArchive'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
										{!! Form::hidden('action',$row->id) !!}
										{!! Form::button('Удалить',['class'=>'btn btn-danger','type'=>'submit']) !!}
										{!! Form::close() !!}
										
										@endcan   

										</td>                     
									</tr>

									@endif
									@endforeach
									@endfor
									@endif
								</tbody>
							</table>
						
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<script type="text/javascript">
	function ConfirmDelete()
	{
		var x = confirm("Вы уверены, что хотите удалить?");
		if (x)
			return true;
		else
			return false;
	}
</script>

@else
<h1>Вы не можете просматривать эту страницу (You cannot view this page)!</h1>
@endcan
@endsection