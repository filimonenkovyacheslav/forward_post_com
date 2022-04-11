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
				<a href="{{ route('exportExcelInvoice') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Экспорт в Excel</a>
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
						<form action="{{ route('invoiceFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="tracking">TRACKING</option>
									<option value="shipper_name">Shipper name</option>
									<option value="shipper_address_phone">Shipper address, phone</option>
									<option value="consignee_name">Consignee name</option>
									<option value="consignee_address">Consignee address</option> 
									<option value="shipped_items">Shipped items</option>
									<option value="weight">Parcel weight, kg</option>
									<option value="height">Parcel height, cm</option>
									<option value="length">Parcel length, cm</option>
									<option value="width">Parcel width, cm</option>
									<option value="declared_value">Declared value</option>  
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
										<th>BOX</th>
										<th>Shipper name</th>
										<th>Shipper address, phone</th>
										<th>Consignee name</th> 
										<th>Consignee address</th>
										<th>Shipped items</th>
										<th>Parcel weight, kg</th>
										<th>Parcel height, cm</th>
										<th>Parcel length, cm</th>
										<th>Parcel width, cm</th>
										<th>Declared value</th>
										<th>Lot</th>
									</tr>
								</thead>
								<tbody>

									@if(isset($invoice_obj))
									@foreach($invoice_obj as $row)

									<tr>
										<td title="{{$row->number}}">
											<div class="div-number">{{$row->number}}</div>
										</td>
										<td title="{{$row->tracking}}">
											<div class="div-3">{{$row->tracking}}</div>
										</td>
										<td title="{{$row->box}}">
											<div class="div-number">{{$row->box}}</div>
										</td>
										<td title="{{$row->shipper_name}}">
											<div class="div-10">{{$row->shipper_name}}</div>
										</td>
										<td title="{{$row->shipper_address_phone}}">
											<div class="div-invoice">{{$row->shipper_address_phone}}</div>
										</td>
										<td title="{{$row->consignee_name}}">
											<div class="div-10">{{$row->consignee_name}}</div>
										</td>
										<td title="{{$row->consignee_address}}">
											<div class="div-invoice">{{$row->consignee_address}}</div>
										</td>
										<td title="{{$row->shipped_items}}">
											<div class="div-invoice">{{$row->shipped_items}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-number">{{$row->weight}}</div>
										</td>
										<td title="{{$row->height}}">
											<div class="div-number">{{$row->height}}</div>
										</td>
										<td title="{{$row->length}}">
											<div class="div-number">{{$row->length}}</div>
										</td>
										<td title="{{$row->width}}">
											<div class="div-number">{{$row->width}}</div>
										</td>
										<td title="{{$row->declared_value}}">
											<div class="div-number">{{$row->declared_value}}</div>
										</td>
										<td title="{{$row->batch_number}}">
											<div class="div-3">{{$row->batch_number}}</div>
										</td>
									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $invoice_obj->appends($data)->links() }}
							@else
							{{ $invoice_obj->links() }}
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