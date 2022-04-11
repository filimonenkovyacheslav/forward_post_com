@extends('layouts.admin')
@section('content')

@can('update-user')
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

					@if(isset($user))				

						{!! Form::open(['url'=>route('userUpdate', ['id'=>$user->id]), 'class'=>'form-horizontal','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('name','Имя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('name',$user->name,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('email','E-mail',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::email('email',$user->email,['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('password','Пароль',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('password','',['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('role','Роль',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('role', $roles_arr, $user->role,['class' => 'form-control']) !!}
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