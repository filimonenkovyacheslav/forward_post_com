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

<div class="content mt-3">
	<div class="animated fadeIn">
		@can('eng-update-post')
		<div class="row">
			<div class="col-md-12">
				<a href="{{ route('exportExcelPhilInd') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Export to Excel</a>
			</div>
		</div>
		@endcan
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
					
					@can('editPost')
					<a class="btn btn-success btn-move" href="{{ route('philIndWorksheetAddColumn') }}">Add column</a>
					@endcan

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ route('philIndWorksheetFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf

							<div class="filter-item">
								<label class="table_columns" style="margin: 0 15px">Choose column:
									<select class="form-control" id="table_columns" name="table_columns[]">
										<option value="" selected="selected"></option>
										<option value="id">Id</option>
										<option value="packing_number">Packing No.</option>
										<option value="date">Date</option>
										<option value="direction">Direction</option>
										<option value="status">Status</option>
										<option value="status_date">Status Date</option>
										<option value="order_date">Order Date</option>
										<option value="tracking_main">Main Tracking number</option>
										<option value="tracking_local">Local tracking number</option>
										<option value="pallet_number">Pallet number</option>
										<option value="comments_1">Comments 1</option>
										<option value="comments_2">Comments 2</option>
										<option value="shipper_name">Shipper\'s name</option>
										<option value="shipper_country">Shipper\'s country</option>
										<option value="shipper_region">Shipper region</option>
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
										<option value="status_ru">Status Ru</option>
										<option value="status_he">Status He</option>                 
									</select>
								</label>
								<label>Filter:
									<input type="search" name="table_filter_value[]" class="form-control form-control-sm">
								</label>
							</div>

							<button type="submit" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Search</button>
							<button type="button" id="filter-item-button" style="margin-left:30px" class="btn btn-primary">Add filter</button>
						</form>
					</div>

					@can('editEngDraft')

					<div class="checkbox-operations">
						
						{!! Form::open(['url'=>route('addPhilIndDataById'), 'onsubmit' => 'return CheckColor(event)', 'class'=>'worksheet-add-form','method' => 'POST']) !!}

						<input type="hidden" name="which_admin" value="en">

						<label>Select action with selected rows:
							<select class="form-control" name="checkbox_operations_select">
								<option value=""></option>

								@can('changeColor')
								<option value="color">Change color</option>
								@endcan

								@can('editPost')										
								<option value="delete">Delete</option>
								<option value="change">Change</option>	
								@endcan

								<option value="double">Double in Draft</option>

								<!-- <option value="cancel-pdf">Cancel PDF</option> -->
								<option value="download-pdf">Download PDF</option>
								<option value="return-eng-draft">Return to Draft</option>
																
							</select>
						</label>

						<label class="checkbox-operations-change">Choose column:
							<select class="form-control" id="phil-ind-tracking-columns" name="phil-ind-tracking-columns">
								<option value="" selected="selected"></option>
								<option value="status">Status</option>
								<option value="tracking_local">Local tracking number</option>
								<option value="pallet_number">Pallet number</option>
								<option value="comments_1">Comments 1</option>
								<option value="comments_2">Comments 2</option>
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

						<label class="checkbox-operations-color">Choose color:
							<select class="form-control" name="tr_color">
								<option value="" selected="selected"></option>
								<option value="transparent">No color</option>
								<option value="tr-orange">Orange</option>
								<option value="tr-yellow">Yellow</option>
								<option value="tr-green">Green</option>
								<option value="tr-blue">Blue</option>
							</select>
						</label>

						<label class="phil-ind-value-by-tracking checkbox-operations-change">Input value:
							<textarea class="form-control" name="value-by-tracking"></textarea>
							<input type="hidden" name="status_ru">
							<input type="hidden" name="status_he">
							<input type="hidden" name="shipper_country_val">
							<input type="hidden" name="consignee_country_val">
						</label>

						{!! Form::button('Save',['class'=>'btn btn-primary checkbox-operations-change','type'=>'submit']) !!}
						{!! Form::close() !!}

						@can('editPost')

						{!! Form::open(['url'=>route('deletePhilIndWorksheetById'),'method' => 'POST']) !!}
						{!! Form::button('Delete',['class'=>'btn btn-danger  checkbox-operations-delete','type'=>'submit','onclick' => 'ConfirmDelete(event)']) !!}
						{!! Form::close() !!}
						
						@endcan

						<form class="checkbox-operations-change-one" action="{{ url('/admin/phil-ind-worksheet/') }}" method="GET">
							@csrf	
						</form>

						<form class="checkbox-operations-double" action="{{ url('/admin/courier-eng-draft-worksheet-double/') }}" method="GET">
							@csrf	
							<input type="hidden" name="duplicate_qty" value="1">
							<input type="hidden" name="worksheet_original" value="1">
						</form>

						<form class="checkbox-operations-return-eng-draft" action="{{ url('/admin/return-eng-draft') }}" method="GET">
							@csrf	
						</form>

						<form class="checkbox-operations-cancel-pdf" action="{{ route('cancelPdf') }}" method="POST">
							@csrf	
							<input type="hidden" name="eng_worksheet_id" class="cancel-pdf">
						</form>

						<form class="checkbox-operations-download-pdf" action="{{ route('downloadAllPdf') }}" method="POST">
							@csrf	
							<input type="hidden" name="id" class="download-pdf">
							<input type="hidden" name="type" value="eng_worksheet_id">
						</form>

					</div>
					
					@endcan
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<!-- <table id="bootstrap-data-table" class="table table-striped table-bordered"> -->
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>V</th>
											<th>Id</th>
											<th>Packing List No.</th>
											<th>Date<hr>
												@can('editPost')
												<a class="btn btn-primary" target="_blank" href="{{ route('showPhilIndStatusDate') }}">Change</a>
												@endcan
											</th>
											<th>Direction</th>
											<th>Status</th>
											<th>Status Date</th>
											<th>Order Date</th>
											<th>Main Tracking number<hr>
												@can('editPost')
												<a class="btn btn-primary" target="_blank" href="{{ route('showPhilIndData') }}">Change</a>
												@endcan
											</th> 
											<th>Order number</th>
											<th>Parcels qty</th>
											<th>Local tracking number</th>
											<th>Pallet number</th>
											<th>Comments 1</th>
											<th>Comments 2</th>
											<th>Shipper's name</th>
											<th>Shipper's country</th>
											<th>Shipper region</th>
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
											<th>District/City</th>
											<th>State pincode</th>
											<th>Consignee's address</th>
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
											<th>Lot<hr>
												@can('editPost')
												<a class="btn btn-primary" target="_blank" href="{{ route('changePhilIndStatus') }}">Change</a>
												@endcan
											</th>
											<th>Payment date and comments</th>
											<th>Amount of payment</th>
											<th>Status Ru</th>
											<th>Status He</th>										
											
											@if($new_column_1)
											<th>{{$new_column_1}}<hr>
												@can('editPost')

												{!! Form::open(['url'=>route('philIndWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDeleteColumn()', 'class'=>'form-horizontal','method' => 'POST']) !!}
												{!! Form::hidden('name_column','new_column_1') !!}
												{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
												{!! Form::close() !!}

												@endcan
											</th>
											@endif
											@if($new_column_2)
											<th>{{$new_column_2}}<hr>
												@can('editPost')

												{!! Form::open(['url'=>route('philIndWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDeleteColumn()', 'class'=>'form-horizontal','method' => 'POST']) !!}
												{!! Form::hidden('name_column','new_column_2') !!}
												{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
												{!! Form::close() !!}

												@endcan
											</th>
											@endif
											@if($new_column_3)
											<th>{{$new_column_3}}<hr>
												@can('editPost')

												{!! Form::open(['url'=>route('philIndWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDeleteColumn()', 'class'=>'form-horizontal','method' => 'POST']) !!}
												{!! Form::hidden('name_column','new_column_3') !!}
												{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
												{!! Form::close() !!}

												@endcan
											</th>
											@endif
											@if($new_column_4)
											<th>{{$new_column_4}}<hr>
												@can('editPost')

												{!! Form::open(['url'=>route('philIndWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDeleteColumn()', 'class'=>'form-horizontal','method' => 'POST']) !!}
												{!! Form::hidden('name_column','new_column_4') !!}
												{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
												{!! Form::close() !!}

												@endcan
											</th>
											@endif
											@if($new_column_5)
											<th>{{$new_column_5}}<hr>
												@can('editPost')

												{!! Form::open(['url'=>route('philIndWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDeleteColumn()', 'class'=>'form-horizontal','method' => 'POST']) !!}
												{!! Form::hidden('name_column','new_column_5') !!}
												{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
												{!! Form::close() !!}

												@endcan
											</th>
											@endif																			

											<th>Consignee's name (for customs)</th>
											<th>Consignee's address (for customs)</th>
											<th>Consignee's phone number (for customs)</th>
											<th>Consignee's ID number (for customs)</th>

										</tr>

									</thead>
									<tbody>

										@if(isset($phil_ind_worksheet_obj))
										@foreach($phil_ind_worksheet_obj as $row)

										<tr class="{{$row->background}}">
											<td class="td-checkbox">
												<input type="hidden" name="old_color[]" value="{{$row->background}}">
												<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
											</td>
											<td title="{{$row->id}}">
												<div class="div-22">{{$row->id}}</div>
											</td>
											<td title="{{$row->getLastDocUniq()}}">
												<div class="div-3">{{$row->getLastDocUniq()}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->date}}">
												<div class="div-3">{{$row->date}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->direction}}">
												<div class="div-3">{{$row->direction}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->status}}">
												<div data-name="status" data-id="{{ $row->id }}" class="div-3">{{$row->status}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->status_date}}">
												<div class="div-3">{{$row->status_date}}</div>
											</td>
											<td class="@can('update-user')allowed-update @endcan" title="{{$row->order_date}}">
												<div data-name="order_date" data-id="{{ $row->id }}" class="div-3">{{$row->order_date}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->tracking_main}}">
												<div data-name="tracking_main" data-id="{{ $row->id }}" class="div-3">{{$row->tracking_main}}</div>
											</td>
											<td class="td-button" title="{{$row->order_number}}">
												<div class="div-22">{{$row->order_number}}</div>
											</td>
											<td title="{{$row->parcels_qty}}">
												<div class="div-22">{{$row->parcels_qty}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->tracking_local}}">
												<div data-name="tracking_local" data-id="{{ $row->id }}" class="div-3">{{$row->tracking_local}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan" title="{{$row->pallet_number}}">
												<div data-name="pallet_number" data-id="{{ $row->id }}" class="div-3">{{$row->pallet_number}}</div>
											</td>
											<td class="@can('editComments-eng')allowed-update @endcan" title="{{$row->comments_1}}">
												<div data-name="comments_1" data-id="{{ $row->id }}" class="div-3">{{$row->comments_1}}</div>
											</td>
											<td class="@can('editColumns-eng')allowed-update @endcan" title="{{$row->comments_2}}">
												<div data-name="comments_2" data-id="{{ $row->id }}" class="div-3">{{$row->comments_2}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_name}}">
												<div data-name="shipper_name" data-id="{{ $row->id }}" class="div-3">{{$row->shipper_name}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_country}}">
												<div data-name="shipper_country" data-id="{{ $row->id }}" class="div-3">{{$row->shipper_country}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_region}}">
												<div data-name="shipper_region" data-id="{{ $row->id }}" class="div-2">{{$row->shipper_region}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_city}}">
												<div data-name="shipper_city" data-id="{{ $row->id }}" class="div-3">{{$row->shipper_city}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->passport_number}}">
												<div data-name="passport_number" data-id="{{ $row->id }}" class="div-3">{{$row->passport_number}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->return_date}}">
												<div data-name="return_date" data-id="{{ $row->id }}" class="div-3">{{$row->return_date}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_address}}">
												<div data-name="shipper_address" data-id="{{ $row->id }}" class="div-3">{{$row->shipper_address}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->standard_phone}}">
												<div data-name="standard_phone" data-id="{{ $row->id }}" class="div-4">{{$row->standard_phone}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_phone}}">
												<div data-name="shipper_phone" data-id="{{ $row->id }}" class="div-3">{{$row->shipper_phone}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipper_id}}">
												<div data-name="shipper_id" data-id="{{ $row->id }}" class="div-3">{{$row->shipper_id}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->consignee_name}}">
												<div data-name="consignee_name" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_name}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->consignee_country}}">
												<div data-name="consignee_country" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_country}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->house_name}}">
												<div data-name="house_name" data-id="{{ $row->id }}" class="div-3">{{$row->house_name}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->post_office}}">
												<div data-name="post_office" data-id="{{ $row->id }}" class="div-3">{{$row->post_office}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->district}}">
												<div data-name="district" data-id="{{ $row->id }}" class="div-3">{{$row->district}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->state_pincode}}">
												<div data-name="state_pincode" data-id="{{ $row->id }}" class="div-3">{{$row->state_pincode}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->consignee_address}}">
												<div data-name="consignee_address" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_address}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->consignee_phone}}">
												<div data-name="consignee_phone" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_phone}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->consignee_id}}">
												<div data-name="consignee_id" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_id}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipped_items}}">
												<div data-name="shipped_items" data-id="{{ $row->id }}" class="div-3">{{$row->shipped_items}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan @if($row->getLastDocUniq())pdf-file @endif" title="{{$row->shipment_val}}">
												<div data-name="shipment_val" data-id="{{ $row->id }}" class="div-3">{{$row->shipment_val}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->operator}}">
												<div data-name="operator" data-id="{{ $row->id }}" class="div-3">{{$row->operator}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->courier}}">
												<div data-name="courier" data-id="{{ $row->id }}" class="div-3">{{$row->courier}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->delivery_date_comments}}">
												<div data-name="delivery_date_comments" data-id="{{ $row->id }}" class="div-3">{{$row->delivery_date_comments}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan" title="{{$row->weight}}">
												<div data-name="weight" data-id="{{ $row->id }}" class="div-3">{{$row->weight}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan" title="{{$row->width}}">
												<div data-name="width" data-id="{{ $row->id }}" class="div-3">{{$row->width}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan" title="{{$row->height}}">
												<div data-name="height" data-id="{{ $row->id }}" class="div-3">{{$row->height}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan" title="{{$row->length}}">
												<div data-name="length" data-id="{{ $row->id }}" class="div-3">{{$row->length}}</div>
											</td>
											<td class="@can('editColumns-eng-2')allowed-update @endcan" title="{{$row->volume_weight}}">
												<div data-name="volume_weight" data-id="{{ $row->id }}" class="div-3">{{$row->volume_weight}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->lot}}">
												<div data-name="lot" data-id="{{ $row->id }}" class="div-3">{{$row->lot}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->payment_date_comments}}">
												<div data-name="payment_date_comments" data-id="{{ $row->id }}" class="div-3">{{$row->payment_date_comments}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->amount_payment}}">
												<div data-name="amount_payment" data-id="{{ $row->id }}" class="div-3">{{$row->amount_payment}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->status_ru}}">
												<div class="div-3">{{$row->status_ru}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->status_he}}">
												<div class="div-3">{{$row->status_he}}</div>
											</td>
											
											@if($new_column_1)
											<td title="{{$row->new_column_1}}">
												<div class="div1">{{$row->new_column_1}}</div>
											</td>
											@endif
											@if($new_column_2)
											<td title="{{$row->new_column_2}}">
												<div class="div1">{{$row->new_column_2}}</div>
											</td>
											@endif
											@if($new_column_3)
											<td title="{{$row->new_column_3}}">
												<div class="div1">{{$row->new_column_3}}</div>
											</td>
											@endif
											@if($new_column_4)
											<td title="{{$row->new_column_4}}">
												<div class="div1">{{$row->new_column_4}}</div>
											</td>
											@endif
											@if($new_column_5)
											<td title="{{$row->new_column_5}}">
												<div class="div1">{{$row->new_column_5}}</div>
											</td>
											@endif										 

											<td class="@can('editPost')allowed-update @endcan" title="{{$row->consignee_name_customs}}">
												<div data-name="consignee_name_customs" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_name_customs}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->consignee_address_customs}}">
												<div data-name="consignee_address_customs" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_address_customs}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->consignee_phone_customs}}">
												<div data-name="consignee_phone_customs" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_phone_customs}}</div>
											</td>
											<td class="@can('editPost')allowed-update @endcan" title="{{$row->consignee_id_customs}}">
												<div data-name="consignee_id_customs" data-id="{{ $row->id }}" class="div-3">{{$row->consignee_id_customs}}</div>
											</td>
											
										</tr>

										@endforeach
										@endif
									</tbody>
								</table>

								@if(isset($data))
								{{ $phil_ind_worksheet_obj->appends($data)->links() }}
								@else
								{{ $phil_ind_worksheet_obj->links() }}
								@endif							
								
							</div>
						</div>
					</div>
				</div><!-- .col-md-12 -->
			</div><!-- .row -->		
			
		</div><!-- .animated -->
	</div><!-- .content -->


	<!-- Modal -->
	<a id="update-cell" data-toggle="modal" data-target="#updateCellModal"></a>

	<div class="modal fade" id="updateCellModal" tabindex="-1" role="dialog" aria-labelledby="updateCellModalLabel" aria-hidden="true" style="background: rgba(0, 0, 0, 0.4);">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="updateCellModalLabel">Update cell</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('addPhilIndDataById') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="form-group phil-ind-value-by-tracking">
							<textarea class="form-control" name="value-by-tracking"></textarea>
							<input type="hidden" name="status_ru">
							<input type="hidden" name="status_he">
							<input type="hidden" name="shipper_country_val">
							<input type="hidden" name="consignee_country_val">
						</div>					
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" style="font-size:.8rem">SAVE</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<script>

		function ConfirmDeleteColumn()
		{
			var x = confirm("Are you sure you want to delete?");
			if (x)
				return true;
			else
				return false;
		}


		function ConfirmDelete(event)
		{
			let href = location.href;
			const newHref = href.split('/admin/')[0];
			event.preventDefault();
			const form = event.target.parentElement;
			const data = new URLSearchParams(new FormData(form)).toString();
			location.href = newHref+'/admin/to-trash?'+data+'&table=phil_ind_worksheet';
		}

		
		function CheckColor(event){
			
			$('.alert.alert-danger').remove();
			const form = event.target;
			const color = document.querySelector('[name="tr_color"]').value;

			if (color) {
				event.preventDefault();
				$.ajax({
					url: '/admin/check-row-color/',
					type: "POST",
					data: $(form).serialize(),
					headers: {
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (data) {
						console.log(data);
						if (data.error) {
							$('.card-header').after(`
								<div class="alert alert-danger">
								`+data.error+`										
								</div>`)
							return 0;
						}
						else{
							form.submit();
						}
					},
					error: function (msg) {
						alert('Admin error');
					}
				});
			}		
		}

	</script>

	@endsection