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
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>
					@can('update-user')
					<a class="btn btn-primary btn-move" href="{{ route('showUser') }}">Добавить</a>
					@endcan 
					<div class="card-body">
						<table id="bootstrap-data-table" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Имя</th>
									<th>E-mail</th>
									<th>Роль</th>
									@can('update-user')
									<th>Изменить</th>
									@endcan 
								</tr>
							</thead>
							<tbody>
								
								@if(isset($users))
								@foreach($users as $user)
								
								<tr>
									<td>{{$user->name}}</td>
									<td>{{$user->email}}</td>
									<td>{{$user->role}}</td>
                                    
                                    @can('update-user')
                                    <td>
                                    	<a class="btn btn-primary" href="{{ url('/admin/users/'.$user->id) }}">Изменить</a>
                                    	{!! Form::open(['url'=>route('deleteUser'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
                                    	{!! Form::hidden('email',$user->email) !!}
                                    	{!! Form::hidden('action',$user->id) !!}
                                    	{!! Form::button('Удалить',['class'=>'btn btn-danger','type'=>'submit']) !!}
                                    	{!! Form::close() !!}
                                    </td>
                                    @endcan 
                                
                                </tr>

                                @endforeach
                                @endif
                            </tbody>
                        </table>
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