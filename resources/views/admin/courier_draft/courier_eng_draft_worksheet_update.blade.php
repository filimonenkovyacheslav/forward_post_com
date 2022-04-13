@extends('layouts.phil_ind_admin')
@section('content')

@can('editEngDraft')

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

					@if(isset($courier_eng_draft_worksheet))				

						{!! Form::open(['url'=>route('courierEngDraftWorksheetUpdate', ['id'=>$courier_eng_draft_worksheet->id]), 'class'=>'form-horizontal china-worksheet-form phil-ind-update-form','method' => 'POST']) !!}

						@can('update-user')
						@php
							$courier_eng_draft_worksheet->date = str_replace(".", "-", $courier_eng_draft_worksheet->date);
						@endphp
						<div class="form-group">
							{!! Form::label('date','Date',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::date('date',$courier_eng_draft_worksheet->date,['class' => 'form-control'])!!}
							</div>
						</div>
						@endcan						

						<div class="form-group">
							{!! Form::label('status','Status',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Pending' => 'Pending', 'Forwarding to the warehouse in the sender country' => 'Forwarding to the warehouse in the sender country', 'At the warehouse in the sender country' => 'At the warehouse in the sender country', 'At the customs in the sender country' => 'At the customs in the sender country', 'Forwarding to the receiver country' => 'Forwarding to the receiver country', 'At the customs in the receiver country' => 'At the customs in the receiver country', 'Forwarding to the receiver' => 'Forwarding to the receiver', 'Delivered' => 'Delivered', 'Return' => 'Return', 'Box' => 'Box', 'Pick up' => 'Pick up', 'Specify' => 'Specify', 'Think' => 'Think', 'Canceled' => 'Canceled'), $courier_eng_draft_worksheet->status,['class' => 'form-control']) !!}
							</div>
						</div>

						@can('update-user')
						<div class="form-group">
							{!! Form::label('status_date','Status date',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::date('status_date',$courier_eng_draft_worksheet->status_date,['class' => 'form-control'])!!}
							</div>
						</div>
						@endcan						
						
						<div class="form-group">
							{!! Form::label('tracking_main','Main tracking number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',$courier_eng_draft_worksheet->tracking_main,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('parcels_qty','Parcels qty',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::number('parcels_qty',$courier_eng_draft_worksheet->parcels_qty,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_local','Local tracking number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_local',$courier_eng_draft_worksheet->tracking_local,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('pallet_number','Pallet number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pallet_number',$courier_eng_draft_worksheet->pallet_number,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comments_1','Comments 1',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_1',$courier_eng_draft_worksheet->comments_1,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comments_2','Comments 2',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_2',$courier_eng_draft_worksheet->comments_2,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_name','Shipper\'s name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_name',$courier_eng_draft_worksheet->shipper_name,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('shipper_country','Shipper\'s country',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('shipper_country', array('Israel' => 'Israel', 'Germany' => 'Germany'), isset($courier_eng_draft_worksheet->shipper_country) ? $courier_eng_draft_worksheet->shipper_country : '',['class' => 'form-control']) !!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_city','Shipper\'s city/village',['class' => 'col-md-2 control-label'])   !!}
							
							@if ($courier_eng_draft_worksheet->shipper_country === 'Israel')

							<div class="col-md-4 choose-city-eng">
								{!! Form::select('choose_city_eng', ['0' => 'City change method', '1' => 'Select from the list (Region will be automatically determined)', '2' => 'Enter manually (Region may not be determined)'],'0',['class' => 'form-control']) !!}
							</div>
							
							<div class="col-md-4 choose-city-eng">
								@if (in_array($courier_eng_draft_worksheet->shipper_city, array_keys($israel_cities)))
								
								{!! Form::select('shipper_city', $israel_cities, isset($courier_eng_draft_worksheet->shipper_city) ? $courier_eng_draft_worksheet->shipper_city : '',['class' => 'form-control']) !!}

								{!! Form::text('shipper_city',$courier_eng_draft_worksheet->shipper_city,['class' => 'form-control','style' => 'display:none','disabled' => 'disabled'])!!}
								
								@else

								{!! Form::select('shipper_city', $israel_cities, isset($courier_eng_draft_worksheet->shipper_city) ? $courier_eng_draft_worksheet->shipper_city : '',['class' => 'form-control','style' => 'display:none','disabled' => 'disabled']) !!}

								{!! Form::text('shipper_city',$courier_eng_draft_worksheet->shipper_city,['class' => 'form-control'])!!}

								@endif

							</div>

							<div class="col-md-8 choose-city-germany" style="display:none">	
								{!! Form::text('shipper_city',$courier_eng_draft_worksheet->shipper_city,['class' => 'form-control','disabled' => 'disabled'])!!}
							</div>
								
							@elseif ($courier_eng_draft_worksheet->shipper_country === 'Germany')

							<div class="col-md-4 choose-city-eng" style="display:none">
								{!! Form::select('choose_city_eng', ['0' => 'City change method', '1' => 'Select from the list (Region will be automatically determined)', '2' => 'Enter manually (Region may not be determined)'],'0',['class' => 'form-control']) !!}
							</div>
							
							<div class="col-md-4 choose-city-eng" style="display:none">
								@if (in_array($courier_eng_draft_worksheet->shipper_city, array_keys($israel_cities)))
								
								{!! Form::select('shipper_city', $israel_cities, isset($courier_eng_draft_worksheet->shipper_city) ? $courier_eng_draft_worksheet->shipper_city : '',['class' => 'form-control','disabled' => 'disabled']) !!}

								{!! Form::text('shipper_city',$courier_eng_draft_worksheet->shipper_city,['class' => 'form-control','style' => 'display:none','disabled' => 'disabled'])!!}
								
								@else

								{!! Form::select('shipper_city', $israel_cities, isset($courier_eng_draft_worksheet->shipper_city) ? $courier_eng_draft_worksheet->shipper_city : '',['class' => 'form-control','style' => 'display:none','disabled' => 'disabled']) !!}

								{!! Form::text('shipper_city',$courier_eng_draft_worksheet->shipper_city,['class' => 'form-control','disabled' => 'disabled'])!!}

								@endif

							</div>
							
							<div class="col-md-8 choose-city-germany">	
								{!! Form::text('shipper_city',$courier_eng_draft_worksheet->shipper_city,['class' => 'form-control'])!!}
							</div>	
								
							@endif
							
						</div>

						<div class="form-group">
							{!! Form::label('passport_number','GSTN/Passport number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('passport_number',$courier_eng_draft_worksheet->passport_number,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('return_date','Estimated return to India date',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('return_date',$courier_eng_draft_worksheet->return_date,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_address','Shipper\'s address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_address',$courier_eng_draft_worksheet->shipper_address,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('standard_phone','Shipper\'s phone number (standard)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('standard_phone',$courier_eng_draft_worksheet->standard_phone,['class' => 'form-control standard-phone'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_phone','Shipper\'s phone number (additionally)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_phone',$courier_eng_draft_worksheet->shipper_phone,['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('shipper_id','Shipper\'s ID number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_id',$courier_eng_draft_worksheet->shipper_id,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_name','Consignee\'s name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name',$courier_eng_draft_worksheet->consignee_name,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_country','Consignee\'s country',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('consignee_country', array('India' => 'India', 'Nepal' => 'Nepal', 'Nigeria' => 'Nigeria', 'Ghana' => 'Ghana', 'Cote D\'Ivoire' => 'Cote D\'Ivoire', 'South Africa' => 'South Africa', 'Thailand' => 'Thailand'), isset($courier_eng_draft_worksheet->consignee_country) ? $courier_eng_draft_worksheet->consignee_country: '',['class' => 'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('house_name','House name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('house_name',$courier_eng_draft_worksheet->house_name,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('post_office','Local post office',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('post_office',$courier_eng_draft_worksheet->post_office,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('district','District/City',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('district',$courier_eng_draft_worksheet->district,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('state_pincode','State pincode',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('state_pincode',$courier_eng_draft_worksheet->state_pincode,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address','Consignee\'s address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address',$courier_eng_draft_worksheet->consignee_address,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_phone','Consignee\'s phone number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone',$courier_eng_draft_worksheet->consignee_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id','Consignee\'s ID number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id',$courier_eng_draft_worksheet->consignee_id,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipped_items','Shipped items',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipped_items',$courier_eng_draft_worksheet->shipped_items,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('shipment_val','Shipment\'s declared value',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_val',$courier_eng_draft_worksheet->shipment_val,['class' => 'form-control'])!!}
							</div>
						</div>

						@can('editPost')

						<div class="form-group">
							{!! Form::label('operator','Operator',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('operator',$courier_eng_draft_worksheet->operator,['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						<div class="form-group">
							{!! Form::label('courier','Courier',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('courier',$courier_eng_draft_worksheet->courier,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('delivery_date_comments','Pick-up/delivery date and comments',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('delivery_date_comments',$courier_eng_draft_worksheet->delivery_date_comments,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('weight','Weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',$courier_eng_draft_worksheet->weight,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('width','Width',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',$courier_eng_draft_worksheet->width,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('height','Height',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',$courier_eng_draft_worksheet->height,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('length','Length',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',$courier_eng_draft_worksheet->length,['class' => 'form-control'])!!}
							</div>
						</div>
												
						<div class="form-group">
							{!! Form::label('volume_weight','Volume weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',$courier_eng_draft_worksheet->volume_weight,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('lot','Lot',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('lot',$courier_eng_draft_worksheet->lot,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('payment_date_comments','Payment date and comments',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('payment_date_comments',$courier_eng_draft_worksheet->payment_date_comments,['class' => 'form-control'])!!}
							</div>
						</div>
												
						<div class="form-group">
							{!! Form::label('amount_payment','Amount of payment',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_payment',$courier_eng_draft_worksheet->amount_payment,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('status_ru_disabled','Status Ru',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ru_disabled',$courier_eng_draft_worksheet->status_ru,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ru',$courier_eng_draft_worksheet->status_ru)!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status_he_disabled','Status He',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',$courier_eng_draft_worksheet->status_he,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',$courier_eng_draft_worksheet->status_he)!!}
							</div>
						</div>																	

						<div class="form-group">
							{!! Form::label('consignee_name_customs','Consignee\'s name (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name_customs',$courier_eng_draft_worksheet->consignee_name_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address_customs','Consignee\'s address (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address_customs',$courier_eng_draft_worksheet->consignee_address_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_phone_customs','Consignee\'s phone number (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone_customs',$courier_eng_draft_worksheet->consignee_phone_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id_customs','Consignee\'s ID number (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id_customs',$courier_eng_draft_worksheet->consignee_id_customs,['class' => 'form-control'])!!}
							</div>
						</div>

							{!! Form::hidden('id',$courier_eng_draft_worksheet->id)!!}

							{!! Form::hidden('shipper_region',$courier_eng_draft_worksheet->shipper_region)!!}

							{!! Form::hidden('in_trash',$courier_eng_draft_worksheet->in_trash)!!}

							{!! Form::hidden('parcels_qty',$courier_eng_draft_worksheet->parcels_qty)!!}

							{!! Form::hidden('date',$courier_eng_draft_worksheet->date,['class' => 'form-control'])!!}

							{!! Form::hidden('direction',$courier_eng_draft_worksheet->direction,['class' => 'form-control'])!!}

							{!! Form::hidden('status',$courier_eng_draft_worksheet->status,['class' => 'form-control'])!!}

							{!! Form::hidden('status_date',$courier_eng_draft_worksheet->status_date)!!}

							{!! Form::hidden('tracking_main',$courier_eng_draft_worksheet->tracking_main,['class' => 'form-control'])!!}

							{!! Form::hidden('parcels_qty',$courier_eng_draft_worksheet->parcels_qty,['class' => 'form-control'])!!}

							{!! Form::hidden('order_number',$courier_eng_draft_worksheet->order_number)!!}

							{!! Form::hidden('tracking_local',$courier_eng_draft_worksheet->tracking_local,['class' => 'form-control'])!!}

							{!! Form::hidden('pallet_number',$courier_eng_draft_worksheet->pallet_number,['class' => 'form-control'])!!}

							{!! Form::hidden('comments_1',$courier_eng_draft_worksheet->comments_1,['class' => 'form-control'])!!}

							{!! Form::hidden('comments_2',$courier_eng_draft_worksheet->comments_2,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_name',$courier_eng_draft_worksheet->shipper_name,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_country',$courier_eng_draft_worksheet->shipper_country)!!}

							{!! Form::hidden('shipper_address',$courier_eng_draft_worksheet->shipper_address,['class' => 'form-control'])!!}

							{!! Form::hidden('standard_phone',$courier_eng_draft_worksheet->standard_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_phone',$courier_eng_draft_worksheet->shipper_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_id',$courier_eng_draft_worksheet->shipper_id,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_name',$courier_eng_draft_worksheet->consignee_name,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_country',$courier_eng_draft_worksheet->consignee_country)!!}

							{!! Form::hidden('consignee_address',$courier_eng_draft_worksheet->consignee_address,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_phone',$courier_eng_draft_worksheet->consignee_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_id',$courier_eng_draft_worksheet->consignee_id,['class' => 'form-control'])!!}

							{!! Form::hidden('shipped_items',$courier_eng_draft_worksheet->shipped_items,['class' => 'form-control'])!!}

							{!! Form::hidden('shipment_val',$courier_eng_draft_worksheet->shipment_val,['class' => 'form-control'])!!}

							{!! Form::hidden('operator',$courier_eng_draft_worksheet->operator,['class' => 'form-control'])!!}

							{!! Form::hidden('courier',$courier_eng_draft_worksheet->courier,['class' => 'form-control'])!!}

							{!! Form::hidden('delivery_date_comments',$courier_eng_draft_worksheet->delivery_date_comments,['class' => 'form-control'])!!}

							{!! Form::hidden('weight',$courier_eng_draft_worksheet->weight,['class' => 'form-control'])!!}

							{!! Form::hidden('width',$courier_eng_draft_worksheet->width,['class' => 'form-control'])!!}

							{!! Form::hidden('height',$courier_eng_draft_worksheet->height,['class' => 'form-control'])!!}

							{!! Form::hidden('length',$courier_eng_draft_worksheet->length,['class' => 'form-control'])!!}

							{!! Form::hidden('volume_weight',$courier_eng_draft_worksheet->volume_weight,['class' => 'form-control'])!!}

							{!! Form::hidden('lot',$courier_eng_draft_worksheet->lot,['class' => 'form-control'])!!}

							{!! Form::hidden('payment_date_comments', $courier_eng_draft_worksheet->payment_date_comments,['class' => 'form-control'])!!}

							{!! Form::hidden('amount_payment',$courier_eng_draft_worksheet->amount_payment,['class' => 'form-control'])!!}

							{!! Form::hidden('status_ru',$courier_eng_draft_worksheet->status_ru,['class' => 'form-control'])!!}

							{!! Form::hidden('status_he',$courier_eng_draft_worksheet->status_he,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_name_customs',$courier_eng_draft_worksheet->consignee_name_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_address_customs',$courier_eng_draft_worksheet->consignee_address_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_phone_customs',$courier_eng_draft_worksheet->consignee_phone_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_id_customs',$courier_eng_draft_worksheet->consignee_id_customs,['class' => 'form-control'])!!}
					
						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

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