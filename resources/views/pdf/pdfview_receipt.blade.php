<style type="text/css">
	td,th {
		min-width: 100px;
		border: 1px solid black;
		padding: 3px;
	}
	.without-border td,.without-border th {
		border: none;
	}
	table{
		font-size: 10px;
		border-collapse: collapse;
		border: 2px solid black;
		text-align: center;
		vertical-align: middle;
		width:400px;
	}
</style>
<center style="font-family: 'DejaVu Sans'">
	
	<table>	
		<tr>
			<th colspan="2" style="font-size:14px">
				RECEIPT {{ $name }}
			</th>
		</tr>	
		<tr class="without-border">			
			<th>CUSTOMER NAME</th>
			<td>{{ $receipt['senderName'] }}</td>
		</tr>
		<tr class="without-border">			
			<th>DATE</th>
			<td>{{ $date }}</td>
		</tr>
		<tr class="without-border">			
			<th>QUANTITY OF PARCELS</th>
			<td>{{ $receipt['quantity'] }}</td>
		</tr>
		<tr class="without-border">			
			<th>AMOUNT</th>
			<td>{{ $receipt['amount'] }}</td>
		</tr>

	</table>
</center>








