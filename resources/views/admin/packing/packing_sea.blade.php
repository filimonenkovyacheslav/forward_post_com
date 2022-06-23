@extends('layouts.admin')
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

@can('editPost')

<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-md-12">
				<a href="{{ route('exportExcelPackingSea') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Экспорт в Excel</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>

					@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@php
						session(['this_previous_url' => url()->full()]);
					@endphp
					
					<div class="card-body packing-sea">
						<div class="table-container">
							<table id="bootstrap-data-table" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Плательщик</th>
										<th>Contract Nr.</th>
										<th>Type</th>
										<th>Trek-KOD</th>
										<th>ФИО Отправителя</th> 
										<th>ФИО получателя</th>
										<th>Код Страны</th>
										<th>Индекс</th>
										<th>Регион</th>
										<th>Район</th>
										<th>Город доставки</th>
										<th>улица</th>
										<th>дом</th>
										<th>корпус</th>
										<th>квартира</th>
										<th>Телефон(+7ххххх)</th>
										<th>Tarif €</th>
										<th>Tarif €-cent</th>
										<th>weight kg</th>
										<th>weight g</th>
										<th>код услуги</th>	
										<th>Amount of COD Rbl</th>
										<th>Amount of COD kop</th>
										<th>номер вложения</th>
										<th>Наименования вложения</th>
										<th>Количество вложений</th>
										<th>weight of enclosures kg</th>
										<th>weight of enclosures g</th>
										<th>стоимость евро</th>
										<th>стоимость евроценты</th>			
									</tr>
								</thead>
								<tbody>

									@if(isset($packing_sea_obj))
									@foreach($packing_sea_obj as $row)

									<tr>
										<td title="{{$row->payer}}">
											<div class="div-3">{{$row->payer}}</div>
										</td>
										<td title="{{$row->contract}}">
											<div class="div-3">{{$row->contract}}</div>
										</td>
										<td title="{{$row->type}}">
											<div class="div-3">{{$row->type}}</div>
										</td>
										<td title="{{$row->track_code}}">
											<div class="div-3">{{$row->track_code}}</div>
										</td>
										<td title="{{$row->full_shipper}}">
											<div class="div-3">{{$row->full_shipper}}</div>
										</td>
										<td title="{{$row->full_consignee}}">
											<div class="div-3">{{$row->full_consignee}}</div>
										</td>
										<td title="{{$row->country_code}}">
											<div class="div-3">{{$row->country_code}}</div>
										</td>
										<td title="{{$row->postcode}}">
											<div class="div-3">{{$row->postcode}}</div>
										</td>
										<td title="{{$row->region}}">
											<div class="div-3">{{$row->region}}</div>
										</td>
										<td title="{{$row->district}}">
											<div class="div-3">{{$row->district}}</div>
										</td>
										<td title="{{$row->city}}">
											<div class="div-3">{{$row->city}}</div>
										</td>
										<td title="{{$row->street}}">
											<div class="div-3">{{$row->street}}</div>
										</td>
										<td title="{{$row->house}}">
											<div class="div-3">{{$row->house}}</div>
										</td>
										<td title="{{$row->body}}">
											<div class="div-3">{{$row->body}}</div>
										</td>
										<td title="{{$row->room}}">
											<div class="div-3">{{$row->room}}</div>
										</td>
										<td title="{{$row->phone}}">
											<div class="div-3">{{$row->phone}}</div>
										</td>
										<td title="{{$row->tariff}}">
											<div class="div-3">{{$row->tariff}}</div>
										</td>
										<td title="{{$row->tariff_cent}}">
											<div class="div-3">{{$row->tariff_cent}}</div>
										</td>
										<td title="{{$row->weight_kg}}">
											<div class="div-3">{{$row->weight_kg}}</div>
										</td>
										<td title="{{$row->weight_g}}">
											<div class="div-3">{{$row->weight_g}}</div>
										</td>
										<td title="{{$row->service_code}}">
											<div class="div-3">{{$row->service_code}}</div>
										</td>
										<td title="{{$row->amount_1}}">
											<div class="div-3">{{$row->amount_1}}</div>
										</td>
										<td title="{{$row->amount_2}}">
											<div class="div-3">{{$row->amount_2}}</div>
										</td>
										<td title="{{$row->attachment_number}}">
											<div class="div-3">{{$row->attachment_number}}</div>
										</td>
										<td title="{{$row->attachment_name}}">
											<div class="div-3">{{$row->attachment_name}}</div>
										</td>
										<td title="{{$row->amount_3}}">
											<div class="div-3">{{$row->amount_3}}</div>
										</td>
										<td title="{{$row->weight_enclosures_kg}}">
											<div class="div-3">{{$row->weight_enclosures_kg}}</div>
										</td>
										<td title="{{$row->weight_enclosures_g}}">
											<div class="div-3">{{$row->weight_enclosures_g}}</div>
										</td>
										<td title="{{$row->value_euro}}">
											<div class="div-3">{{$row->value_euro}}</div>
										</td>
										<td title="{{$row->value_cent}}">
											<div class="div-3">{{$row->value_cent}}</div>
										</td>										
                                                               
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

@endcan

<script>

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection