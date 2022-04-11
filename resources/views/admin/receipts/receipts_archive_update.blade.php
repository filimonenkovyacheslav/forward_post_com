@extends('layouts.phil_ind_admin')
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

					@if(isset($receipt))				

						{!! Form::open(['url'=>route('receiptsArchiveUpdate', ['id'=>$receipt->id]), 'class'=>'form-horizontal receipt-update-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('comment','Комментарий (Comment)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comment',$receipt->comment,['class' => 'form-control'])!!}
							</div>
						</div>

						{!! Form::hidden('id',$receipt->id)!!}

						{!! Form::hidden('receipt_id',$receipt->receipt_id)!!}

						{!! Form::hidden('worksheet_id',$receipt->worksheet_id)!!}

						{!! Form::hidden('which_admin',$receipt->which_admin)!!}

						{!! Form::hidden('receipt_number',$receipt->receipt_number)!!}

						{!! Form::hidden('tracking_main',$receipt->tracking_main)!!}

						{!! Form::hidden('description',$receipt->description)!!}

						{!! Form::hidden('update_date',$receipt->update_date)!!}

						{!! Form::hidden('status',$receipt->status)!!}
					
						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

					@endif
					
					</div>
				</div>
			</div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection