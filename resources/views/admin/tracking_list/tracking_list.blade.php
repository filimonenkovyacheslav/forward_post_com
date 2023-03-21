@extends('layouts.phil_ind_admin')
@section('content')
@can('changeColor')
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
						<form action="{{ route('trackingListFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="list_name">List name</option>
									<option value="tracking">Tracking No.</option>                
								</select>
							</label>
							<label>Filter:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>

							<input type="hidden" name="for_active">
							
							<button type="button" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Search</button>
						</form>
					
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Action</th>
										<th>List name</th>
										<th>Tracking No.</th>				
									</tr>

								</thead>
								<tbody>

									@if(isset($tracking_list_obj))
									@foreach($tracking_list_obj as $row)

									<tr>
										<td class="td-button">

											<form class="form-horizontal" action="{{ route('exportTrackingList') }}" method="POST">
												@csrf	
												<input type="hidden" name="list_name" value="{{$row->list_name}}">
												<button class="btn btn-primary" type="submit">Export</button>
											</form>

											@can('editPost')
											
											{!! Form::open(['url'=>route('trackingListDelete'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->list_name) !!}
											{!! Form::button('Remove all list',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
											
										</td>
										<td title="{{$row->list_name}}">
											<div style="width:250px">{{$row->list_name}}</div>
										</td>
										<td title="{{$row->tracking}}">
											<div class="div-4">{{$row->tracking}}</div>
										</td>                                                  
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $tracking_list_obj->appends($data)->links() }}
							@else
							{{ $tracking_list_obj->links() }}
							@endif							
						
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
		var x = confirm("Are you sure?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@else
<h1>You cannot view this page!</h1>
@endcan
@endsection