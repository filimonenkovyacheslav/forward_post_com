@extends('layouts.phil_ind_admin')
@section('content')
@can('editPost')
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
						<form action="{{ route('newReceiptsFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="name">Receipt name</option>
									<option value="created_at">Date</option>                
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
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Action</th>
										<th>Receipt name</th>
										<th>Date</th>				
									</tr>

								</thead>
								<tbody>

									@if(isset($new_receipts))
									@foreach($new_receipts as $row)

									<tr>
										<td class="td-button">
											<a class="btn btn-primary" href="{{ url('/download-new-receipt').'/'.$row->id }}">Download</a>
										</td>
										<td title="{{$row->name}}">
											<div style="width:250px">{{$row->name}}</div>
										</td>
										<td title="{{$row->created_at}}">
											<div class="div-4">{{$row->created_at}}</div>
										</td>                                                  
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data) && isset($new_receipts))
							{{ $new_receipts->appends($data)->links() }}
							@elseif(isset($new_receipts))
							{{ $new_receipts->links() }}
							@endif							
						
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan
@endsection