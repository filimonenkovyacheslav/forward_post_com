@extends('layouts.admin')
@section('content')
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
				<a href="{{ route('exportExcel') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Экспорт в Excel</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<!-- <div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div> -->

					@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					<div class="card-body worksheet">
						<div class="table-container">
							<table id="bootstrap-data-table" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Номер</th>
										<th>Дата
											@can('editPost')
											<a class="btn btn-primary" target="_blank" href="{{ url('/admin/worksheet/date') }}">Изменить</a>
											@endcan
										</th>
										<th>Направление</th>
										<th>Статус</th>
										<th>Локальный</th>
										<th>Трекинг</th>
										<th>Коммент менеджера</th>
										<th>Коммент</th>
										<th>Комментарии</th>
										<th>Отправитель</th>
										<th>Данные отправителя</th>
										<th>Получатель</th>
										<th>Данные получателя</th>
										<th>E-mail получателя</th>
										<th>Декларируемая стоимость посылки, $</th>
										<th>Упаковка</th>
										<th>Оплачивает посылку</th>
										<th>Трекинг номер и вес посылки</th>
										<th>Ширина</th>
										<th>Высота</th>
										<th>Длина</th>
										<th>Номер партии
											@can('editPost')
											<a class="btn btn-primary" target="_blank" href="{{ url('/admin/worksheet/batch-number') }}">Изменить</a>
											@endcan
										</th>
										<th>Тип отправления</th>
										<th>Описание содержимого посылки</th>
										<th>1. позиция: описание количество цена,$</th>
										<th>2. позиция: описание количество цена,$</th>
										<th>3. позиция</th>
										<th>4. позиция</th>
										<th>5. позиция</th>
										<th>6. позиция</th>
										<th>7. позиция</th>
										<th>ENG</th>
										<th>RU</th>
										<th>HE</th>
										<th>UA</th>
										<th>Оплата</th>
										<th>Физ. вес</th>
										<th>Объем. вес</th>
										<th>К-во ед</th>
										<th>Комментарии</th>
										<th>Себестоимость</th>
										<th>Стоимость доставки из Украины</th>
										<th>Изменить</th>
									</tr>
								</thead>
								<tbody>
									
									@if(isset($worksheet_obj))
									@foreach($worksheet_obj as $row)
									
									<tr>
										<td>{{$row->num_row}}</td>
										<td>{{$row->date}}</td>
										<td>{{$row->direction}}</td>
										<td>{{$row->status}}</td>
										<td>{{$row->local}}</td>
										<td>{{$row->tracking}}</td>
										<td>{{$row->manager_comments}}</td>
										<td>{{$row->comment}}</td>
										<td>{{$row->comments}}</td>
										<td>{{$row->sender}}</td>
										<td>{{$row->data_sender}}</td>
										<td>{{$row->recipient}}</td>
										<td>{{$row->data_recipient}}</td>
										<td>{{$row->email_recipient}}</td>
										<td>{{$row->parcel_cost}}</td>
										<td>{{$row->packaging}}</td>
										<td>{{$row->pays_parcel}}</td>
										<td>{{$row->number_weight}}</td>
										<td>{{$row->width}}</td>
										<td>{{$row->height}}</td>
										<td>{{$row->length}}</td>
										<td>{{$row->batch_number}}</td>
										<td>{{$row->shipment_type}}</td>
										<td>{{$row->parcel_description}}</td>
										<td>{{$row->position_1}}</td>
										<td>{{$row->position_2}}</td>
										<td>{{$row->position_3}}</td>
										<td>{{$row->position_4}}</td>
										<td>{{$row->position_5}}</td>
										<td>{{$row->position_6}}</td>
										<td>{{$row->position_7}}</td>
										<td>{{$row->guarantee_text_en}}</td>
										<td>{{$row->guarantee_text_ru}}</td>
										<td>{{$row->guarantee_text_he}}</td>
										<td>{{$row->guarantee_text_ua}}</td>
										<td>{{$row->payment}}</td>
										<td>{{$row->phys_weight}}</td>
										<td>{{$row->volume_weight}}</td>
										<td>{{$row->quantity}}</td>
										<td>{{$row->comments_2}}</td>
										<td>{{$row->cost_price}}</td>
										<td>{{$row->shipment_cost}}</td> 
										<td class="td-button">
											@can('editPost')
											<a class="btn btn-primary" href="{{ url('/admin/worksheet-update/'.$row->id) }}">Изменить</a>

											{!! Form::open(['url'=>route('deleteWorksheet'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Удалить',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</td>                              
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>		
	
	</div><!-- .animated -->
</div><!-- .content -->

<script>

	function ConfirmDelete()
	{
		var x = confirm("Вы уверены, что хотите удалить?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection