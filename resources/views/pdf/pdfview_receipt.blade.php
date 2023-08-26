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
	@page { sheet-size: 120mm 200mm; }
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
			<th colspan="2" style="font-size:22px;padding-bottom:50px;text-align:center;">
				החשבונית שלך
			</th>
		</tr>	
		<tr class="without-border">
			<th style="padding-bottom:30px;">
				נתקבל מ 
			</th>
			<th style="text-align:left;padding-bottom:30px;">
				{{ $receipt['senderName'] }}
			</th>
		</tr>
		<tr class="without-border">
			
		</tr>
		<tr class="without-border">
			<th colspan="2" style="text-align:center;font-size:28px;padding-bottom:50px;">
				{{ $receipt['amount'] }} ₪ 
			</th>
		</tr>
		<tr class="without-border">			
			<th style="padding-bottom:20px;">שם פריט</th>
			<th style="padding-bottom:20px;text-align:left;">משלוח בינלאומי</th>
		</tr>
		<tr class="without-border">			
			<th style="padding-bottom:20px;">כמות</th>
			<th style="padding-bottom:20px;text-align:left;">{{ $receipt['quantity'] }}</th>
		</tr>
		<tr class="without-border">			
			<th style="padding-bottom:30px;">סכום לתשלום</th>
			<th style="padding-bottom:30px;text-align:left;">{{ $receipt['amount'].'.00' }} ₪</th>
		</tr>
		<tr class="without-border">			
			<th>מזומן</th>
			<th style="text-align:left;">{{ $receipt['amount'].'.00' }} ₪</th>
		</tr>
		<tr class="without-border">			
			<th style="padding-bottom:80px;">תאריך</th>
			<th style="padding-bottom:80px;text-align:left;">{{ $date }}</th>
		</tr>
		<tr class="with-border"><td></td><td></td></tr>
		<tr class="without-border">
			<th colspan="2">
				משלוחים בינלאומיים.
			</th>
		</tr>
		<tr class="without-border">
			<th colspan="2">
				התחיה 7 כפר סבא 
			</th>
		</tr>
		<tr class="without-border">
			<th colspan="2">
				0559909659
			</th>
		</tr>

	</table>
</center>

</body>
</html>







