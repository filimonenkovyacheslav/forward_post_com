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

					<div class="card-body">
						<table id="bootstrap-data-table" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Роль</th>
									<th>Имя</th>									
									@can('update-user')
									<th>Изменить</th>
									@endcan 
								</tr>
							</thead>
							<tbody>

								@for($i=0; $i < count($viewer_arr); $i++)

								<tr>
									@php
									$td = false;
									@endphp
								<td>{{$viewer_arr[$i]}}</td>
								
								@if(count($partners))
									
									@foreach($partners as $partner)
										
										@if($partner->role === $viewer_arr[$i])
										
										<td>{{$partner->name}}</td>
										@php
										$td = true;
										@endphp
										@break
							
										@endif
                                
	                                @endforeach
	                                @if(!$td)
	                                <td></td>
	                                @endif
                                
                                @else
									<td></td>
                                @endif

                                @can('update-user')
                                    <td>
                                    	<a class="btn btn-primary" href="{{ url('/admin/partners-up/'.$viewer_arr[$i]) }}">Изменить</a>
                                    </td>
                                @endcan 

                                </tr>
                                
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->


@endsection