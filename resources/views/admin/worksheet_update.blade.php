@extends('layouts.admin')
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

					@if(isset($worksheet))				

						{!! Form::open(['url'=>route('worksheetUpdate', ['id'=>$worksheet->id]), 'class'=>'form-horizontal old-worksheet-update-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('num_row','Номер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('num_row',$worksheet->num_row,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('date','Дата',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('date',$worksheet->date,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('direction','Направление',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('direction',$worksheet->direction,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status','Статус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Доставляется на склад в стране отправителя' => 'Доставляется на склад в стране отправителя', 'На складе в стране отправителя' => 'На складе в стране отправителя', 'На таможне в стране отправителя' => 'На таможне в стране отправителя', 'Доставляется в страну получателя' => 'Доставляется в страну получателя', 'На таможне в стране получателя' => 'На таможне в стране получателя', 'Доставляется получателю' => 'Доставляется получателю', 'Доставлено' => 'Доставлено', 'Возврат' => 'Возврат', 'Коробка' => 'Коробка', 'Забрать' => 'Забрать', 'Уточнить' => 'Уточнить', 'Думают' => 'Думают'), $worksheet->status,['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('local','Локальный',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('local',$worksheet->local,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tracking','Трекинг',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking',$worksheet->tracking,['class' => 'form-control'])!!}
							</div>
						</div>						
						<div class="form-group">
							{!! Form::label('manager_comments','Коммент менеджера',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('manager_comments',$worksheet->manager_comments,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('comment','Коммент',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comment',$worksheet->comment,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('comments','Комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments',$worksheet->comments,['class' => 'form-control'])!!}
							</div>
						</div>					
						<div class="form-group">
							{!! Form::label('sender','Отправитель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender',$worksheet->sender,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('data_sender','Данные отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('data_sender',$worksheet->data_sender,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient','Получатель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient',$worksheet->recipient,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('data_recipient','Данные получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('data_recipient',$worksheet->data_recipient,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('email_recipient','E-mail получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('email_recipient',$worksheet->email_recipient,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('parcel_cost','Декларируемая стоимость посылки, $',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('parcel_cost',$worksheet->parcel_cost,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('packaging','Упаковка',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('packaging',$worksheet->packaging,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pays_parcel','Оплачивает посылку',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pays_parcel',$worksheet->pays_parcel,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('number_weight','Трекинг номер и вес посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('number_weight',$worksheet->number_weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('width','Ширина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',$worksheet->width,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('height','Высота',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',$worksheet->height,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('length','Длина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',$worksheet->length,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('batch_number','Номер партии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('batch_number',$worksheet->batch_number,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('shipment_type','Тип отправления',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_type',$worksheet->shipment_type,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('parcel_description','Описание содержимого посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('parcel_description',$worksheet->parcel_description,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_1','1. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_1',$worksheet->position_1,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_2','2. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_2',$worksheet->position_2,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_3','3. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_3',$worksheet->position_3,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_4','4. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_4',$worksheet->position_4,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_5','5. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_5',$worksheet->position_5,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_6','6. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_6',$worksheet->position_6,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('position_7','7. позиция',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('position_7',$worksheet->position_7,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('guarantee_text_ru','RU',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('guarantee_text_ru',$worksheet->guarantee_text_ru,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('payment','Оплата',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('payment',$worksheet->payment,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('phys_weight','Физ. вес',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('phys_weight',$worksheet->phys_weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('volume_weight','Объемный вес',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',$worksheet->volume_weight,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('quantity','К-во ед',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('quantity',$worksheet->quantity,['class' => 'form-control'])!!}
							</div>
						</div>						
						<div class="form-group">
							{!! Form::label('comments_2','Комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_2',$worksheet->comments_2,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('cost_price','Себестоимость',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('cost_price',$worksheet->cost_price,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('shipment_cost','Стоимость доставки из Украины',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_cost',$worksheet->shipment_cost,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_en_disabled','Статус (ENG)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_en_disabled',$worksheet->guarantee_text_en,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('guarantee_text_en',$worksheet->guarantee_text_en,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_he_disabled','Статус (HE)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',$worksheet->guarantee_text_he,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('guarantee_text_he',$worksheet->guarantee_text_he,[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_ua_disabled','Статус (UA)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ua_disabled',$worksheet->guarantee_text_ua,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('guarantee_text_ua',$worksheet->guarantee_text_ua,[])!!}
							</div>
						</div>

						{!! Form::button('Сохранить',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

						@endif
					
					</div>
				</div>
			</div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

@else
<h1>Вы не можете просматривать эту страницу!</h1>
@endcan 
@endsection