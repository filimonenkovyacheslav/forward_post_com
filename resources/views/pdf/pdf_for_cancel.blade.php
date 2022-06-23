<style type="text/css">
	td {
		min-width: 100px;
	}
</style>
@php
if($type === 'draft_id' || $type === 'worksheet_id') $ru = true;
else $ru = false;
@endphp
<center>
	<table>		
		@if($ru)
		<tr>
			<td colspan="5">
				<img src="{{ asset('/images/cancel_img_1.png') }}">
				<h4 style="margin: 50px auto; width: 300px">					
					{{ $old_document->uniq_id }}
				</h4>
			</td>
		</tr>
		@if($worksheet->tracking_main)
		<tr>
			<td colspan="5">
				<img src="{{ asset('/images/cancel_img_2.png') }}">
				<h4 style="margin: 50px auto; width: 300px">
					{{ $worksheet->tracking_main }}					
				</h4>
			</td>
		</tr>
		@endif
		@elseif(!$ru)
		<tr>
			<td colspan="5">
				<h4 style="margin: 50px auto; width: 300px">{{ $message }}</h4>				
			</td>
		</tr>
		@endif		
		<tr>			
			<td><h3>Date</h3></td>
			<td>{{ $document->date }}</td>
			<td></td>
			<td><h3>Signature</h3></td>
			<td><img src="{{ asset('/upload/signatures/'.$document->signature_for_cancel) }}" style="width:120px;height:100px"></td>
		</tr>
	</table>
</center>






