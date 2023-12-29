<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body style="font-family: 'Cairo', sans-serif;">

<style type="text/css">
	@page { sheet-size: 120mm 170mm; }
	td,th {
		min-width: 100px;
		border: 1px solid black;
		padding-left: 30px;
		text-align: right;
	}
	.without-border td,.without-border th {
		border: none;
	}
	.with-border td {
		border-bottom: none;
		border-left: none;
		border-right: none;
	}
	table{
		font-size: 18px;
		border-collapse: collapse;
		border: 2px solid black;
		vertical-align: middle;
		width:300px;
		border: none;
	}
</style>
<center dir="rtl">
	
	<table>	
		<tr class="without-border">
			<th colspan="2" style="font-size:22px;text-align:center;">
				GCS-DELIVERIES
			</th>
		</tr>	
		<tr class="without-border">
			<th colspan="2" style="text-align:center;">
				הראשונים 13 פתח תקווה 
			</th>
		</tr>
		<tr class="without-border">
			<th colspan="2" style="text-align:center;padding-bottom:70px;">
				972559912684+ 
			</th>
		</tr>
		<tr class="without-border">
			<th style="padding-bottom:70px;">
				לכבוד
			</th>
			<th style="padding-bottom:70px;text-align:left;">
				{{ $receipt['senderName'] }}
			</th>
		</tr>
		<tr class="without-border">	
			<th style="padding-bottom:20px;">פירוט</th>		
			<th style="padding-bottom:20px;text-align:left;">משלוח לחו״ל</th>			
		</tr>
		<tr class="without-border">			
			<th style="padding-bottom:20px;">כמות</th>
			<th style="padding-bottom:20px;text-align:left;">{{ $receipt['quantity'] }}</th>
		</tr>
		<tr class="without-border">			
			<th style="padding-bottom:70px;">סכום</th>
			<th style="padding-bottom:70px;text-align:left;">{{ $receipt['amount'].'.00' }} ₪</th>
		</tr>
		<tr class="without-border">			
			<th>תאריך</th>
			<th style="text-align:left;">{{ $date }}</th>
		</tr>
	</table>
</center>

</body>
</html>







