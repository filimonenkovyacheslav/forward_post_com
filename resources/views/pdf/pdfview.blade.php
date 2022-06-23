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
				PACKING LIST {{ $document->uniq_id }}
			</th>
		</tr>
		<tr>
			<th>DATE</th>
			<td>{{ $document->date }}</td>
			<th>TRACKING No.</th>	
			<td>{{ $tracking }}</td>		
		</tr>
		<tr>
			<th>Weight</th>
			<td>{{ $worksheet->weight }}</td>
			<th>Dimensions</th>	
			<td>{{ $worksheet->width}}x{{$worksheet->height}}x{{$worksheet->length }}</td>		
		</tr>			
		<tr>
			<th colspan="2">SHIPPER</th>
			<th colspan="2">RECEIVER</th>
		</tr>	
		<tr>
			<th>FIRST NAME</th>
			<td>{{ explode(' ',$worksheet->shipper_name)[0] }}</td>
			<th>FIRST NAME</th>	
			<td>{{ explode(' ',$worksheet->consignee_name)[0] }}</td>		
		</tr>
		<tr>
			<th>LASST NAME</th>
			<td>{{ explode(' ',$worksheet->shipper_name)[1] }}</td>
			<th>LASST NAME</th>	
			<td>{{ explode(' ',$worksheet->consignee_name)[1] }}</td>		
		</tr>	
		<tr>
			<th rowspan="3">ADDRESS</th>
			<td rowspan="3">{{ $worksheet->shipper_address }}</td>
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
			<th>CITY</th>
			<td>{{ $worksheet->shipper_city }}</td>
			<th>STATE PINCODE</th>	
			<td>{{ $worksheet->state_pincode }}</td>		
		</tr>
		<tr>
			<th>STATE / COUNTRY </th>
			<td>{{ $worksheet->shipper_country }}</td>
			<th>COUNTRY</th>	
			<td>{{ $worksheet->consignee_country }}</td>		
		</tr>
		<tr>
			<th>PHONE / MOBILE</th>
			<td>{{ $worksheet->standard_phone }}</td>
			<th>PHONE / MOBILE</th>	
			<td>{{ $worksheet->consignee_phone }}</td>		
		</tr>
		<tr>
			<th colspan="4">SHIPPED ITEMS</th>
		</tr>
		<tr>
			<th>No.</th>
			<th colspan="2">Description of Goods</th>
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
				The goods in this parcel are handed over by me to the transporters and are my personal goods. I accept that the transporters hold no responsibility for shortage or damage or content of cargo.
				<br>
				This parcel includes goods then bear no commercial value and are not for sale.
				<br>
				I guarantee that I received the detailed information on items restricted for shipment to the destianation country. None of the items has been included in the the parcel.
				<br>
				I provided true and complete information about the items shipped in this parcsel. In case of any false or incomplete data I recognize my obligation to cover all legal penalties in origin, destination, and transit countries as well as to pay the costs incurred through my fault caused with delays in customs clearance and/or return of the parcel from a warehouse in Israel or from the destination country to me.
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






