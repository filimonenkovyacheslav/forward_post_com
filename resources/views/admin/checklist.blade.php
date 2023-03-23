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
									
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->	

		@can('editPost')

		<div class="row">
			<div class="card" style="margin:15px">	
				<div class="card-header">
					<strong class="card-title">Checklist</strong>
				</div>
				<div style="display:flex">

					<div class="col-md-6">
						<form action="{{ route('importChecklist') }}" method="POST" enctype="multipart/form-data">
							@csrf
							<label>Import to base
								<input type="file" name="import_file">
							</label>

							<button type="submit" style="margin-right:30px" class="btn btn-success">Upload</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		@endcan

		<div class="card-body new-worksheet">
			<div class="table-container">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Tracking No.</th>				
						</tr>

					</thead>
					<tbody>

						@if(isset($checklist_obj))
						@foreach($checklist_obj as $row)

						<tr>
							<td title="{{$row->tracking_main}}">
								<div class="div-4">{{$row->tracking_main}}</div>
							</td>                                                  
						</tr>

						@endforeach
						@endif
					</tbody>
				</table>

				@if(isset($data))
				{{ $checklist_obj->appends($data)->links() }}
				@else
				{{ $checklist_obj->links() }}
				@endif							

			</div>
		</div>
		
	</div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan
@endsection