<style type="text/css">
	td,th {
		min-width: 100px;
		border: 1px solid black;
		padding: 5px;
	}
	table{
		font-size: 10px;
		border-collapse: collapse;
		border: 2px solid black;
		text-align: center;
		vertical-align: middle;
	}
</style>
<center style="font-family: 'DejaVu Sans'">	
	@if ($cancel)
	<h1 style="color:red">CANCELED</h1>
	@endif
	
	<table>
		<tr>
			<th colspan="4" style="font-size:14px">
				<p style="font-size:16px">{{ $brand }}</p>
				<span style="font-style: italic">PACKING LIST {{ $document->uniq_id }}</span>
			</th>
		</tr>
		<tr style="font-style: italic">
			<th>Date</th>
			<td>{{ $document->date }}</td>
			<th>Dimensions</th>	
			<td>{{ $worksheet->width}}x{{$worksheet->height}}x{{$worksheet->length }}</td>
					
		</tr>
		<tr style="font-style: italic">
			<th>TRACKING NO</th>	
			<td>{{ $tracking }}</td>
			<th>Freight Cost</th>
			<td>{{ $worksheet->shipment_val }}</td>					
		</tr>			
		<tr>
			<th colspan="2">SENDER</th>
			<th colspan="2">RECIPIENT</th>
		</tr>	
		<tr>
			<th>FULL NAME</th>
			<td>{{ $worksheet->shipper_name }}</td>
			<th>FULL NAME</th>	
			<td>{{ $worksheet->consignee_name }}</td>		
		</tr>	
		<tr>
			<th rowspan="5">ADDRESS</th>
			<td rowspan="5">{{ $worksheet->shipper_address }}</td>
			<th>HOUSE NAME / ADDRESS</th>	
			<td>{{ ($worksheet->house_name)?$worksheet->house_name.' / ':'' }}{{ $worksheet->consignee_address }}</td>		
		</tr>
		<tr>
			<th>LOCAL POST OFFICE</th>	
			<td>{{ $worksheet->post_office }}</td>		
		</tr>
		<tr>
			<th>DISTRICT / CITY</th>	
			<td>{{ $worksheet->district }}</td>		
		</tr>
		<tr>
			<th>STATE PINCODE</th>	
			<td>{{ $worksheet->state_pincode }}</td>		
		</tr>
		<tr>
			<th>COUNTRY</th>	
			<td>{{ $worksheet->consignee_country }}</td>		
		</tr>
		<tr>
			<th>PHONE</th>
			<td>{{ $worksheet->standard_phone }}</td>
			<th>PHONE</th>	
			<td>{{ $worksheet->consignee_phone }}</td>		
		</tr>
		<tr>
			<th colspan="4">DESCRIPTION OF THE ITEMS SHIPPED</th>
		</tr>
		<tr>
			<th>No.</th>
			<th colspan="2">Description</th>
			<th>Quantity</th>		
		</tr>
		@php
		$number = 1;
		$items = explode(";", $worksheet->shipped_items);            
		@endphp

		@foreach($items as $item) 
			@if(strripos($item, ':') !== false)
			<tr>
				<td>{{$number}}</td>
				<td colspan="2">{{explode(":", $item)[0]}}</td>
				<td>{{explode(":", $item)[1]}}</td>
			</tr>
			@elseif(strripos($item, '-') !== false)
			<tr>
				<td>{{$number}}</td>
				<td colspan="2">{{explode("-", $item)[0]}}</td>
				<td>{{explode("-", $item)[1]}}</td>
			</tr>			
			@endif	
			@php
			$number++;
			@endphp		
		@endforeach
		<tr>
			<th colspan="3">Declared value :</th>	
			<td>{{ $worksheet->shipment_val }}</td>		
		</tr>
		<tr>
			<td colspan="4" style="text-align: left;">
				Hereby by signing this packing list I declare:
				<br>
				1) The goods in this parcel are handed over by me to the transporters and are my personal goods. Courier / Logistics Companies are just facilitators for shipping my cargo / goods - and hold no responsibility for breakage / shortage / damage or content of cargo is my responsibility - and we abide my all laws of local country as there , and well versed for same. 
				<br>
				2) Further these used/old if household goods then bear no commercial value and are not for sale. 
				<br>
				3) I guarantee that I provided true and complete information about the items shipped in this parcel. In case of any false or incomplete data I recognize my obligation to cover all legal penalties in origin, destination, and transit countries as well as to pay the costs incurred through my fault caused with delays in customs clearance and/or return of the parcel from a warehouse in Israel or from the destination country to me.
				<br>
				I am aware of that the forwarding company is not responsible for any delay in delivery has occurred due to circumstances beyond its control, in particular, due to delays in customs clearance, and agree with this, as the terms of service for the parcel delivery.				
			</td>
		</tr>
		<tr>
			<th>CUSTOMER NAME</th>	
			<td colspan="3">{{ $worksheet->shipper_name }}</td>		
		</tr>
		<tr>
			<th>CUSTOMER SIGNATURE</th>	
			<td colspan="3"><img src="{{ asset('/upload/signatures/'.$document->signature) }}" style="width:200px;height:100px"></td>		
		</tr>
	</table>
</center>






