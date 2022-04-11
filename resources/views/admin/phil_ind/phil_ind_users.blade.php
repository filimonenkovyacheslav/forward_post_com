@extends('layouts.phil_ind_admin')
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
<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>
					@can('update-user')
					<a class="btn btn-primary btn-move" href="{{ route('showPhilIndUser') }}">Add</a>
					@endcan 
					<div class="card-body">
						<table id="bootstrap-data-table" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Name</th>
									<th>E-mail</th>
									<th>Role</th>
									@can('update-user')
									<th>Change</th>
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
                                    	<a class="btn btn-primary" href="{{ url('/admin/phil-ind-users/'.$user->id) }}">Change</a>
                                    	{!! Form::open(['url'=>route('deletePhilIndUser'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
                                    	{!! Form::hidden('email',$user->email) !!}
                                    	{!! Form::hidden('action',$user->id) !!}
                                    	{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
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
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection