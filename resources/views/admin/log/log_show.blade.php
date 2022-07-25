@extends('layouts.phil_ind_admin')
@section('content')

@can('changeColor')

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
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>
					<div class="card-body">	

					@if(isset($log))				

						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Field</th>
									<th>Data</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><div style="width:200px">Packing No.</div></td>
									<td><div style="width:900px">{{$log->packing_num}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Site name</div></td>
									<td><div style="width:900px">{{$log->site_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Date</div></td>
									<td><div style="width:900px">{{$log->date}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Direction</div></td>
									<td><div style="width:900px">{{$log->direction}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Tariff</div></td>
									<td><div style="width:900px">{{$log->tariff}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Status</div></td>
									<td><div style="width:900px">{{$log->status}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Status date</div></td>
									<td><div style="width:900px">{{$log->status_date}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Tracking number main</div></td>
									<td><div style="width:900px">{{$log->tracking_main}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Parcels qty</div></td>
									<td><div style="width:900px">{{$log->parcels_qty}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Local tracking number</div></td>
									<td><div style="width:900px">{{$log->tracking_local}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Pallet number</div></td>
									<td><div style="width:900px">{{$log->pallet_number}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Comments 1</div></td>
									<td><div style="width:900px">{{$log->comments_1}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Comments 2</div></td>
									<td><div style="width:900px">{{$log->comments_2}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s name</div></td>
									<td><div style="width:900px">{{$log->shipper_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s country</div></td>
									<td><div style="width:900px">{{$log->shipper_country}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s city/village</div></td>
									<td><div style="width:900px">{{$log->shipper_city}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">GSTN/Passport number</div></td>
									<td><div style="width:900px">{{$log->passport_number}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Estimated return to India date</div></td>
									<td><div style="width:900px">{{$log->return_date}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s address</div></td>
									<td><div style="width:900px">{{$log->shipper_address}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s phone number (standard)</div></td>
									<td><div style="width:900px">{{$log->standard_phone}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s phone number (additionally)</div></td>
									<td><div style="width:900px">{{$log->shipper_phone}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipper\'s ID number</div></td>
									<td><div style="width:900px">{{$log->shipper_id}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Consignee\'s name</div></td>
									<td><div style="width:900px">{{$log->consignee_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Consignee\'s country</div></td>
									<td><div style="width:900px">{{$log->consignee_country}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">House name</div></td>
									<td><div style="width:900px">{{$log->house_name}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Local post office</div></td>
									<td><div style="width:900px">{{$log->post_office}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Region</div></td>
									<td><div style="width:900px">{{$log->region}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">District/City</div></td>
									<td><div style="width:900px">{{$log->district}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">State pincode</div></td>
									<td><div style="width:900px">{{$log->state_pincode}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Consignee\'s address</div></td>
									<td><div style="width:900px">{{$log->consignee_address}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Recipient street</div></td>
									<td><div style="width:900px">{{$log->recipient_street}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Body</div></td>
									<td><div style="width:900px">{{$log->body}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Recipient apartment</div></td>
									<td><div style="width:900px">{{$log->recipient_room}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Consignee\'s phone number</div></td>
									<td><div style="width:900px">{{$log->consignee_phone}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Consignee\'s ID number</div></td>
									<td><div style="width:900px">{{$log->consignee_id}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipped items</div></td>
									<td><div style="width:900px">{{$log->shipped_items}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Shipment\'s declared value</div></td>
									<td><div style="width:900px">{{$log->shipment_val}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Operator</div></td>
									<td><div style="width:900px">{{$log->operator}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Courier</div></td>
									<td><div style="width:900px">{{$log->courier}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Pick-up/delivery date and comments</div></td>
									<td><div style="width:900px">{{$log->delivery_date_comments}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Weight</div></td>
									<td><div style="width:900px">{{$log->weight}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Width</div></td>
									<td><div style="width:900px">{{$log->width}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Height</div></td>
									<td><div style="width:900px">{{$log->height}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Length</div></td>
									<td><div style="width:900px">{{$log->length}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Volume weight</div></td>
									<td><div style="width:900px">{{$log->volume_weight}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Lot</div></td>
									<td><div style="width:900px">{{$log->lot}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Things qty</div></td>
									<td><div style="width:900px">{{$log->quantity_things}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Payment date and comments</div></td>
									<td><div style="width:900px">{{$log->payment_date_comments}}</div></td>
								</tr>
								<tr>
									<td><div style="width:200px">Amount of payment</div></td>
									<td><div style="width:900px">{{$log->amount_payment}}</div></td>
								</tr>
							</tbody>
						</table>
						@endif

						
					
					</div>
				</div>
			</div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection