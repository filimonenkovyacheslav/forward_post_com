@extends('layouts.phil_ind_admin')
@section('content')
@can('update-user')
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

					@if (session('status-error'))
					<div class="alert alert-danger">
						{{ session('status-error') }}
					</div>
					@elseif (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@php
						session(['this_previous_url' => url()->full()]);
					@endphp

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ route('updatesArchiveFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="worksheet_id">Worksheet Id</option>
									<option value="eng_worksheet_id">Eng Worksheet Id</option>
									<option value="draft_id">Draft Id</option>
									<option value="eng_draft_id">Eng Draft Id</option>
									<option value="updates_date">Updates Date</option>
									<option value="user_name">User Name</option>
									<option value="column_name">Column Name</option>
									<option value="old_data">Old Data</option>
                					<option value="new_data">New Data</option>
								</select>
							</label>
							<label>Filter:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>

							<input type="hidden" name="for_active">
							
							<button type="submit" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Search</button>
						</form>
					
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<!-- <table id="bootstrap-data-table" class="table table-striped table-bordered"> -->
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>V</th>
										<th>Delete</th>
										<th>Worksheet Id</th>
										<th>Eng Worksheet Id</th>
										<th>Draft Id</th>
										<th>Eng Draft Id</th>
										<th>Updates Date</th>
										<th>User Name</th>
										<th>Column Name</th>
										<th>Old Data</th>
										<th>New Data</th>			
									</tr>

								</thead>
								<tbody>

									@php
									$id_arr = [];
									@endphp

									@if(isset($filter_arr))
									@for($i=0; $i < count($filter_arr); $i++)
									@foreach($filter_arr[$i] as $row)

									@if (!in_array($row->id, $id_arr))
									@php
									$id_arr[] = $row->id;
									@endphp
									
									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>
										<td class="td-button">

											{!! Form::open(['url'=>route('deleteUpdatesArchive'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}
											
										</td>
										<td title="{{$row->worksheet_id}}">
											<div class="div-number">{{$row->worksheet_id}}</div>
										</td>
										<td title="{{$row->eng_worksheet_id}}">
											<div class="div-number">{{$row->eng_worksheet_id}}</div>
										</td>
										<td title="{{$row->draft_id}}">
											<div class="div-number">{{$row->draft_id}}</div>
										</td>
										<td title="{{$row->eng_draft_id}}">
											<div class="div-number">{{$row->eng_draft_id}}</div>
										</td>
										<td title="{{$row->updates_date}}">
											<div class="div-3">{{$row->updates_date}}</div>
										</td>
										<td title="{{$row->user_name}}">
											<div class="div-3">{{$row->user_name}}</div>
										</td>
										<td title="{{$row->column_name}}">
											<div class="div-3">{{$row->column_name}}</div>
										</td>
										<td title="{{$row->old_data}}">
											<div class="div-3">{{$row->old_data}}</div>
										</td>
										<td title="{{$row->new_data}}">
											<div class="div-3">{{$row->new_data}}</div>
										</td>
                                                           
									</tr>

									@endif
									@endforeach
									@endfor
									@endif
								</tbody>
							</table>

							<div class="checkbox-operations">

								<label>Select action with selected rows:
									<select class="form-control" name="checkbox_operations_select">
										<option value=""></option>
										<option value="delete">Delete</option>
									</select>
								</label>	

								<label class="value-by-tracking checkbox-operations-change">
								</label>

								{!! Form::open(['url'=>route('destroyArchiveById'),'onsubmit' => 'return ConfirmDelete()','method' => 'POST']) !!}
								{!! Form::button('Delete',['class'=>'btn btn-danger  checkbox-operations-delete','type'=>'submit']) !!}
								{!! Form::close() !!}

							</div>						
						
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
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

@else
<h1>Вы не можете просматривать эту страницу (You cannot view this page)!</h1>
@endcan
@endsection