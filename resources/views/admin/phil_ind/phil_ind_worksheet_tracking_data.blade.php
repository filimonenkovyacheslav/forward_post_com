@extends('layouts.phil_ind_admin')
@section('content')

@can('editPost')
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

						{!! Form::open(['url'=>route('addPhilIndData'), 'class'=>'form-horizontal worksheet-add-form','method' => 'POST']) !!}

						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::button('Cancel selection',['class'=>'btn btn-danger', 'onclick' => 'handleCencel()']) !!}						

						<div id="checkbox-group">

						@foreach ($date_arr as $number)  
							<div class="form-group">
							{!! Form::label('tracking[]', $number,['class' => 'col-md-2 control-label'])   !!}
								<div class="col-md-1">
								{!! Form::checkbox('tracking[]', $number, '', ['onclick' => 'handleCheckbox(this)']) !!}
								</div>
							</div>
						@endforeach	

						</div>	

						<label>Choose column:
							<select class="form-control" id="phil-ind-tracking-columns" name="phil-ind-tracking-columns">
								<option value="" selected="selected"></option>
				                <option value="date">Date</option>
				                <option value="direction">Direction</option>
				                <option value="status">Status</option>
				                <option value="tracking_local">Local tracking number</option>
				                <option value="pallet_number">Pallet number</option>
				                <option value="comments_1">Comments 1</option>
				                <option value="comments_2">Comments 2</option>
				                <option value="shipper_name">Shipper's name</option>
				                <option value="shipper_country">Shipper's country</option>
				                <option value="shipper_address">Shipper's address</option>
				                <option value="shipper_phone">Shipper's phone number</option>
				                <option value="shipper_id">Shipper's ID number</option>
				                <option value="consignee_name">Consignee's name</option>
				                <option value="consignee_country">Consignee's country</option>
				                <option value="consignee_address">Consignee's address</option>
				                <option value="consignee_phone">Consignee's phone number</option>
				                <option value="consignee_id">Consignee's ID number</option>
				                <option value="shipped_items">Shipped items</option>
				                <option value="shipment_val">Shipment's declared value</option>
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

						<label class="phil-ind-value-by-tracking">Input value:
							<input class="form-control" type="text" name="value-by-tracking">
							<input type="hidden" name="status_ru">
							<input type="hidden" name="status_he">
							<input type="hidden" name="shipper_country_val">
							<input type="hidden" name="consignee_country_val">
						</label>							

						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::button('Cancel selection',['class'=>'btn btn-danger', 'onclick' => 'handleCencel()']) !!}
						{!! Form::close() !!}

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