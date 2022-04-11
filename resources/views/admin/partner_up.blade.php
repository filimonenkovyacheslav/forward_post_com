@extends('layouts.admin')
@section('content')

@can('update-partner')
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

						{!! Form::open(['url'=>route('partnerUpdate', ['role'=>$role]), 'class'=>'form-horizontal','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('name','Имя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('name',$name,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('role_disabled','Роль',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('role_disabled',$role,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('role', $role,[]) !!}
							</div>
						</div>						

						{!! Form::button('Сохранить',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

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