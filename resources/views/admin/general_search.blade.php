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
						<form action="{{ route('generalSearch') }}" method="POST" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<!-- <option value="table_name">Table name</option> -->
									<option value="id">Id</option>
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
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->	

		@can('editPost')

		<!-- <div class="row">
			<div class="card" style="margin:15px">	
				<div class="card-header">
					<strong class="card-title">Tracking numbers RU</strong>
				</div>
				<div style="display:flex">

					<div class="col-md-6">
						<form action="{{ route('importTrackings') }}" method="POST" enctype="multipart/form-data">
							@csrf
							<label>Import to base
								<input type="file" name="import_file">
							</label>

							<button type="submit" style="margin-right:30px" class="btn btn-success">Upload</button>
						</form>
					</div>

					<div class="col-md-6">
						<a href="{{ route('exportTrackings') }}" style="margin-top: 20px;" class="btn btn-success">Export tracking numbers</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="card" style="margin:15px">	
				<div class="card-header">
					<strong class="card-title">Tracking numbers ENG</strong>
				</div>
				<div style="display:flex">

					<div class="col-md-6">
						<form action="{{ route('importTrackingsEng') }}" method="POST" enctype="multipart/form-data">
							@csrf
							<label>Import to base
								<input type="file" name="import_file">
							</label>

							<button type="submit" style="margin-right:30px" class="btn btn-primary">Upload</button>
						</form>
					</div>

					<div class="col-md-6">
						<a href="{{ route('exportTrackingsEng') }}" style="margin-top: 20px;" class="btn btn-primary">Export tracking numbers</a>
					</div>
				</div>
			</div>
		</div> -->

		@endcan
		
	</div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan
@endsection