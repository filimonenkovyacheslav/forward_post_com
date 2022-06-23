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
				<a href="{{ route('exportExcelManifest') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Экспорт в Excel</a>
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
						<form action="{{ route('manifestFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="tracking">TRACKING</option>
									<option value="sender_country">Sender country</option>
									<option value="sender_name">Sender company</option>
									<option value="recipient_name">Recipient</option>  
									<option value="recipient_city">Recipient city</option>
									<option value="recipient_address">Recipient address</option> 
									<option value="content">Description ENG</option>
									<option value="quantity">Quantity</option> 
									<option value="weight">Weight of item (kg)</option>
									<option value="cost">Customs Cost of item (USD)</option> 
									<option value="batch_number">Lot</option>             
								</select>
							</label>
							<label>Фильтр:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							<button type="button" id="table_filter_button" style="margin-left:35px" class="btn btn-default">Искать</button>
						</form>
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>№</th>
										<th>TRACKING</th>
										<th>Sender country</th>
										<th>Sender company</th>
										<th>Recipient</th> 
										<th>Recipient city</th>
										<th>Recipient address</th>
										<th>Description ENG</th>
										<th>Quantity</th>
										<th>Weight of item (kg)</th>
										<th>Customs Cost of item (USD)</th>
										<th>Lot</th>
									</tr>
								</thead>
								<tbody>

									@if(isset($manifest_obj))
									@foreach($manifest_obj as $row)

									<tr>
										<td title="{{$row->number}}">
											<div class="div-number">{{$row->number}}</div>
										</td>
										<td title="{{$row->tracking}}">
											<div class="div-3">{{$row->tracking}}</div>
										</td>
										<td title="{{$row->sender_country}}">
											<div class="div-3">{{$row->sender_country}}</div>
										</td>
										<td title="{{$row->sender_name}}">
											<div class="div-3">{{$row->sender_name}}</div>
										</td>
										<td title="{{$row->recipient_name}}">
											<div class="div-3">{{$row->recipient_name}}</div>
										</td>
										<td title="{{$row->recipient_city}}">
											<div class="div-3">{{$row->recipient_city}}</div>
										</td>
										<td title="{{$row->recipient_address}}">
											<div class="div-3">{{$row->recipient_address}}</div>
										</td>
										<td title="{{$row->content}}">
											<div class="div-3">{{$row->content}}</div>
										</td>
										<td title="{{$row->quantity}}">
											<div class="div-number">{{$row->quantity}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-number">{{$row->weight}}</div>
										</td>
										<td title="{{$row->cost}}">
											<div class="div-number">{{$row->cost}}</div>
										</td>
										<td title="{{$row->batch_number}}">
											<div class="div-3">{{$row->batch_number}}</div>
										</td>
									
									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $manifest_obj->appends($data)->links() }}
							@else
							{{ $manifest_obj->links() }}
							@endif
						
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<script>

	function ConfirmDelete()
	{
		var x = confirm("Вы уверены, что хотите удалить?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection