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
	}
	table img{
		width:200px;
		height:100px;
	}
</style>
<center style="font-family: 'DejaVu Sans'">

	@if ($cancel)
	<h1 style="color:red">CANCELED</h1>
	@endif
	
	<table>	
		<tr>
			<th colspan="4" style="font-size:14px">
				ПАКИНГ-ЛИСТ {{ $document->uniq_id }}
			</th>
		</tr>	
		<tr class="without-border">			
			<th>Номер посылки</th>
			<td>{{ $tracking }}</td>
			<th>Вес посылки</th>
			<td>{{ $worksheet->weight }}</td>
		</tr>
		<tr class="without-border">			
			<th>Дата</th>
			<td>{{ $document->date }}</td>
			<th>Габариты посылки</th>
			<td>{{ $worksheet->width}}x{{$worksheet->height}}x{{$worksheet->length }}</td>
		</tr>
		<tr>
			<th colspan="2">
				ИНФОРМАЦИЯ ОБ ОТПРАВИТЕЛЕ
			</th>
			<th colspan="2">
				ИНФОРМАЦИЯ О ПОЛУЧАТЕЛЕ
			</th>
		</tr>
		<tr>
			<th>Имя</th>
			<td>{{ $worksheet->sender_name }}</td>
			<th>Имя</th>
			<td>{{ $worksheet->recipient_name }}</td>
		</tr>	
		<tr>
			<th>Страна</th>
			<td>{{ $worksheet->sender_country }}</td>
			<th>Страна</th>
			<td>{{ $worksheet->recipient_country }}</td>
		</tr>
		<tr>
			<th>Адрес</th>
			<td>{{ $worksheet->sender_address }}</td>
			<th>Адрес</th>
			<td>{{ $worksheet->recipient_postcode.', ' }}{{ ($worksheet->region) ? $worksheet->region.', ':'' }}{{ ($worksheet->district) ? $worksheet->district.', ':'' }}{{ $worksheet->recipient_city.', ' }}{{ $worksheet->recipient_street.', ' }}{{ $worksheet->recipient_house.', ' }}{{ ($worksheet->body) ? $worksheet->body.', ':'' }}{{ $worksheet->recipient_room  }}</td>
		</tr>
		<tr>
			<th>Номер телефона</th>
			<td>{{ $worksheet->standard_phone }}</td>
			<th>Номер телефона</th>
			<td>{{ $worksheet->recipient_phone }}</td>
		</tr>	
		<tr>
			<th colspan="4">
				ОПИСАНИЕ СОДЕРЖИМОГО ПОСЫЛКИ
			</th>
		</tr>
		<tr>
			<th>№</th>
			<th colspan="2">ОПИСАНИЕ</td>
			<th>Количество</th>
		</tr>
		@php
		$number = 1;
		$items = explode(";", $worksheet->package_content);            
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
			<th colspan="3">Декларируемая стоимость посылки</td>
			<td>{{ $worksheet->package_cost }}</td>
		</tr>
		<tr style="text-align:left;">
			<td colspan="4">
				<h3>ГАРАНТИЙНЫЕ ОБЯЗАТЕЛЬСТВА ОТПРАВИТЕЛЯ:</h3>
				<p>Я, {{ $worksheet->sender_name }}, нижеподписавшийся/нижеподписавшаяся, подтверждаю, что являюсь отправителем всех вышеуказанных предметов, перечисленных в этом упаковочном листе, включая прилагаемый дополнительный подписанный упаковочный лист (если таковой имеется), и что я лично их упаковал. Подписывая форму, я гарантирую следующее:</p>
				<ol>
					<li>в этом подробном упаковочном листе указано точное и правильное количество предметов, содержащихся в коробке;</li>
					<li>НЕТ необъявленных, запрещенных, незаконных или запрещенных предметов, включая:
						<ul>
							<li>лекарства</li>
							<li>скоропортящиеся продукты</li>
							<li>оружие</li>
							<li>наркотики</li>
							<li>органические материалы</li>
							<li>боеприпасы</li>
							<li>горючие предметы</li>
							<li>наличные деньги</li>
							<li>ювелирные изделия</li>
							<li>порнографические материалы</li>
						</ul>
					</li>
					<li>все предметы, содержащиеся в коробке, предназначены исключительно для личного некоммерческого использования.</li>
					<li>в случае нарушения данных гарантий я согласен оплатить расходы, понесенные по моей вине, вызванные задержками в таможенном оформлении и / или возврате посылки со склада в Израиле или из страны назначения мне.</li>
				</ol>
				<p>Я предупрежден о том, что фирма-перевозчик не несёт ответственность за задержку доставки, произошедшую в силу независящих от нее обстоятельств, в частности, по причине задержки таможенного оформления, и согласен с этим, как с условием предоставления услуги по доставке посылки. Подписывая эту форму, я подтверждаю, что я прочитал и понял все письменные и прилагаемые положения и условия</p>
			</td>
		</tr>
		<tr class="without-border">			
			<th>Имя отправителя</th>
			<td>{{ $worksheet->sender_name }}</td>
			<th>Подпись отправителя</th>
			<td><img src="{{ asset('/upload/signatures/'.$document->signature) }}"></td>
		</tr>
	</table>
</center>








