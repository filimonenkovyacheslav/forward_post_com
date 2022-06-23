@extends('layouts.phil_ind_admin')
@section('content')

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

@can('editColumns-eng')

<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-md-12">
				<a href="{{ route('exportExcelPackingEng') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Export to Excel</a>
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
					
					<div class="card-body packing-eng">
						<div class="table-container">
							<table id="bootstrap-data-table" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Tracking Number</th>
										<th>Destination Country</th>
										<th>Shipper name</th>
										<th>Shipper address</th>
										<th>Shipper Phone No.</th>
										<th>Shipper ID No.</th>
										<th>Consignee name</th>
										<th>Consignee address</th>
										<th>Consignee Phone No.</th>
										<th>Consignee ID No.</th>										
										<th>Dimensions (length)</th>
										<th>Dimensions (width)</th>
										<th>Dimensions (height)</th>
										<th>Weight</th>
										<th>Items enclosed</th>	
										<th>Declared Value</th>			
									</tr>
								</thead>
								<tbody>

									@if(isset($packing_eng_obj))
									@foreach($packing_eng_obj as $row)

									<tr>
										<td title="{{$row->tracking}}">
											<div class="div-3">{{$row->tracking}}</div>
										</td>
										<td title="{{$row->country}}">
											<div class="div-3">{{$row->country}}</div>
										</td>
										<td title="{{$row->shipper_name}}">
											<div class="div-3">{{$row->shipper_name}}</div>
										</td>
										<td title="{{$row->shipper_address}}">
											<div class="div-3">{{$row->shipper_address}}</div>
										</td>
										<td title="{{$row->shipper_phone}}">
											<div class="div-3">{{$row->shipper_phone}}</div>
										</td>
										<td title="{{$row->shipper_id}}">
											<div class="div-3">{{$row->shipper_id}}</div>
										</td>
										<td title="{{$row->consignee_name}}">
											<div class="div-3">{{$row->consignee_name}}</div>
										</td>
										<td title="{{$row->consignee_address}}">
											<div class="div-3">{{$row->consignee_address}}</div>
										</td>
										<td title="{{$row->consignee_phone}}">
											<div class="div-3">{{$row->consignee_phone}}</div>
										</td>
										<td title="{{$row->consignee_id}}">
											<div class="div-3">{{$row->consignee_id}}</div>
										</td>
										<td title="{{$row->length}}">
											<div class="div-3">{{$row->length}}</div>
										</td>
										<td title="{{$row->width}}">
											<div class="div-3">{{$row->width}}</div>
										</td>
										<td title="{{$row->height}}">
											<div class="div-3">{{$row->height}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-3">{{$row->weight}}</div>
										</td>
										<td title="{{$row->items}}">
											<div class="div-3">{{$row->items}}</div>
										</td>
										<td title="{{$row->shipment_val}}">
											<div class="div-3">{{$row->shipment_val}}</div>
										</td>
                                                               
									</tr>

									@endforeach
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

@endcan

<script>

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection