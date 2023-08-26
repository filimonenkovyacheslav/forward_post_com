@extends('layouts.phil_ind_admin')
@section('content')
@can('changeColor')
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
						<form action="{{ route('logsFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<!-- <option value="table_name">Table name</option> -->
									<option value="worksheet_id">Id</option>
									<option value="packing_num">Packing No.</option>
									<option value="date">Date</option>
									<option value="direction">Direction</option>
									<option value="status">Status</option>
									<option value="status_date">Status Date</option>
									<option value="tracking_main">Main Tracking number</option>
									<option value="tracking_local">Local tracking number</option>
									<option value="pallet_number">Pallet number</option>
									<option value="comments_1">Comments 1</option>
									<option value="comments_2">Comments 2</option>
									<option value="shipper_name">Shipper\'s name</option>
									<option value="shipper_country">Shipper\'s country</option>
									<option value="shipper_city">Shipper\'s city/village</option>
									<option value="passport_number">GSTN/Passport number</option>
									<option value="return_date">Estimated return to India date</option>
									<option value="shipper_address">Shipper\'s address</option>
									<option value="standard_phone">Shipper\'s phone (standard)</option>
									<option value="shipper_phone">Shipper\'s phone (additionally)</option>
									<option value="shipper_id">Shipper\'s ID number</option>
									<option value="consignee_name">Consignee\'s name</option>
									<option value="consignee_country">Consignee\'s country</option>
									<option value="house_name">House name</option>
									<option value="post_office">Local post office</option>
									<option value="district">District/City</option>
									<option value="state_pincode">State pincode</option>
									<option value="consignee_address">Consignee\'s address</option>
									<option value="consignee_phone">Consignee\'s phone number</option>
									<option value="consignee_id">Consignee\'s ID number</option>
									<option value="shipped_items">Shipped items</option>
									<option value="shipment_val">Shipment\'s declared value</option>
									<option value="operator">Operator</option>
									<option value="courier">Courier</option>
									<option value="delivery_date_comments">Pick-up/delivery date and comments</option>
									<option value="weight">Weight</option>
									<option value="width">Width</option>
									<option value="height">Height</option>
									<option value="length">Length</option>
									<option value="volume_weight">Volume weight</option>
									<option value="lot">Lot</option>
									<option value="payment_date_comments">Payment date and comments</option>
									<option value="amount_payment">Amount of payment</option>                
								</select>
							</label>
							<label>Filter:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>

							<input type="hidden" name="for_active">
							
							<button type="submit" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Search</button>
						</form>
					
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<!-- <table id="bootstrap-data-table" class="table table-striped table-bordered"> -->
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Show</th>
										<th>Table</th>
										<th>Id</th>
										<th>Packing No.</th>
										<th>Site name</th>
										<th>Date</th>
										<th>Direction</th>
										<th>Tariff</th>
										<th>Status</th>
										<th>Status Date</th>
										<th>Partner</th>
										<th>Main Tracking number</th> 
										<th>Parcels qty</th>
										<th>Local tracking number</th>
										<th>Pallet number</th>
										<th>Comments 1</th>
										<th>Comments 2</th>
										<th>Shipper's name</th>
										<th>Shipper's country</th>
										<th>Shipper\'s city/village</th>
										<th>GSTN/Passport number</th>
										<th>Estimated return to India date</th>
										<th>Shipper's address</th>
										<th>Shipper's phone number (standard)</th>
										<th>Shipper's phone number (additionally)</th>
										<th>Shipper's ID number</th>
										<th>Consignee's name</th>
										<th>Consignee's country</th>
										<th>House name</th>
										<th>Local post office</th>
										<th>Region</th>
										<th>District/City</th>
										<th>State pincode</th>
										<th>Consignee's address</th>
										<th>Recipient street</th>										
										<th>№ корпуса</th>
										<th>Recipient room</th>
										<th>Consignee's phone number</th>
										<th>Consignee's ID number</th>
										<th>Shipped items</th>
										<th>Shipment's declared value</th>
										<th>Operator</th>
										<th>Courier</th>
										<th>Pick-up/delivery date and comments</th>
										<th>Weight</th>
										<th>Width</th>
										<th>Height</th>
										<th>Length</th>
										<th>Volume weight</th>										
										<th>Lot</th>
										<th>Quantity things</th>
										<th>Payment date and comments</th>
										<th>Amount of payment</th>
										<th>Status Ru</th>
										<th>Status He</th>
										<th>Status Ua</th>						
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
									
									@php
									$table = $row->table_name;
									switch($table) {
										case 'new_worksheet';
										$table_name = 'Новый рабочий лист';	
										break;
										case 'phil_ind_worksheet';
										$table_name = 'Work sheet';
										break;
										case 'courier_draft_worksheet';
										$table_name = 'Черновик';
										$type = 'draft_id';
										break;
										case 'courier_eng_draft_worksheet';
										$table_name = 'Draft';
										$type = 'eng_draft_id';
										break;       
										default:
										$table_name = '';
										break;
									}
									@endphp

									<tr>
										<td class="td-button">

											<a class="btn btn-success" data-id="{{ $row->id }}"  href="{{ url('/admin/log-show/'.$row->id) }}">Show</a>

											<form class="checkbox-operations-download-pdf" action="{{ route('downloadAllPdf') }}" method="POST">
												@csrf	
												<input type="hidden" name="id" class="download-pdf" value="{{$row->worksheet_id}}">
												<input type="hidden" name="type" value="{{$type}}">
												<button class="btn btn-primary" type="submit">Packing</button>
											</form>
											
										</td>
										<td title="{{$table_name}}">
											<div class="div-3">{{$table_name}}</div>
										</td>
										<td title="{{$row->worksheet_id}}">
											<div class="div-3">{{$row->worksheet_id}}</div>
										</td>
										<td title="{{$row->packing_num}}">
											<div class="div-3">{{$row->packing_num}}</div>
										</td>
										<td title="{{$row->site_name}}">
											<div class="div-3">{{$row->site_name}}</div>
										</td>
										<td title="{{$row->date}}">
											<div class="div-3">{{$row->date}}</div>
										</td>
										<td title="{{$row->direction}}">
											<div class="div-3">{{$row->direction}}</div>
										</td>
										<td title="{{$row->tariff}}">
											<div class="div-3">{{$row->tariff}}</div>
										</td>
										<td title="{{$row->status}}">
											<div class="div-3">{{$row->status}}</div>
										</td>
										<td title="{{$row->status_date}}">
											<div class="div-3">{{$row->status_date}}</div>
										</td>
										<td title="{{$row->partner}}">
											<div class="div-3">{{$row->partner}}</div>
										</td>
										<td title="{{$row->tracking_main}}">
											<div class="div-3">{{$row->tracking_main}}</div>
										</td>
										<td title="{{$row->parcels_qty}}">
											<div class="div-22">{{$row->parcels_qty}}</div>
										</td>
										<td title="{{$row->tracking_local}}">
											<div class="div-3">{{$row->tracking_local}}</div>
										</td>
										<td title="{{$row->pallet_number}}">
											<div class="div-3">{{$row->pallet_number}}</div>
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
											<div class="div-3">{{$row->shipper_country}}</div>
										</td>
										<td title="{{$row->shipper_city}}">
											<div class="div-3">{{$row->shipper_city}}</div>
										</td>
										<td title="{{$row->passport_number}}">
											<div class="div-3">{{$row->passport_number}}</div>
										</td>
										<td title="{{$row->return_date}}">
											<div class="div-3">{{$row->return_date}}</div>
										</td>
										<td title="{{$row->shipper_address}}">
											<div class="div-3">{{$row->shipper_address}}</div>
										</td>
										<td title="{{$row->standard_phone}}">
											<div class="div-4">{{$row->standard_phone}}</div>
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
										<td title="{{$row->consignee_country}}">
											<div class="div-3">{{$row->consignee_country}}</div>
										</td>
										<td title="{{$row->house_name}}">
											<div class="div-3">{{$row->house_name}}</div>
										</td>
										<td title="{{$row->post_office}}">
											<div class="div-3">{{$row->post_office}}</div>
										</td>
										<td title="{{$row->region}}">
											<div class="div-3">{{$row->region}}</div>
										</td>
										<td title="{{$row->district}}">
											<div class="div-3">{{$row->district}}</div>
										</td>
										<td title="{{$row->state_pincode}}">
											<div class="div-3">{{$row->state_pincode}}</div>
										</td>
										<td title="{{$row->consignee_address}}">
											<div class="div-3">{{$row->consignee_address}}</div>
										</td>
										<td title="{{$row->recipient_street}}">
											<div class="div-3">{{$row->recipient_street}}</div>
										</td>										
										<td title="{{$row->body}}">
											<div class="div-3">{{$row->body}}</div>
										</td>
										<td title="{{$row->recipient_room}}">
											<div class="div-3">{{$row->recipient_room}}</div>
										</td>
										<td title="{{$row->consignee_phone}}">
											<div class="div-3">{{$row->consignee_phone}}</div>
										</td>
										<td title="{{$row->consignee_id}}">
											<div class="div-3">{{$row->consignee_id}}</div>
										</td>
										<td title="{{$row->shipped_items}}">
											<div class="div-3">{{$row->shipped_items}}</div>
										</td>
										<td title="{{$row->shipment_val}}">
											<div class="div-3">{{$row->shipment_val}}</div>
										</td>
										<td title="{{$row->operator}}">
											<div class="div-3">{{$row->operator}}</div>
										</td>
										<td title="{{$row->courier}}">
											<div class="div-3">{{$row->courier}}</div>
										</td>
										<td title="{{$row->delivery_date_comments}}">
											<div class="div-3">{{$row->delivery_date_comments}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-3">{{$row->weight}}</div>
										</td>
										<td title="{{$row->width}}">
											<div class="div-3">{{$row->width}}</div>
										</td>
										<td title="{{$row->height}}">
											<div class="div-3">{{$row->height}}</div>
										</td>
										<td title="{{$row->length}}">
											<div class="div-3">{{$row->length}}</div>
										</td>
										<td title="{{$row->volume_weight}}">
											<div class="div-3">{{$row->volume_weight}}</div>
										</td>
										<td title="{{$row->lot}}">
											<div class="div-3">{{$row->lot}}</div>
										</td>
										<td title="{{$row->quantity_things}}">
											<div class="div-3">{{$row->quantity_things}}</div>
										</td>
										<td title="{{$row->payment_date_comments}}">
											<div class="div-3">{{$row->payment_date_comments}}</div>
										</td>
										<td title="{{$row->amount_payment}}">
											<div class="div-3">{{$row->amount_payment}}</div>
										</td>
										<td title="{{$row->status_ru}}">
											<div class="div-3">{{$row->status_ru}}</div>
										</td>
										<td title="{{$row->status_he}}">
											<div class="div-3">{{$row->status_he}}</div>
										</td>									 
										<td title="{{$row->status_ua}}">
											<div class="div-3">{{$row->status_ua}}</div>
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

<script>

	function ConfirmActivate()
	{
		var x = confirm("Are you sure you want to activate?");
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