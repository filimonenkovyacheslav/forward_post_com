@extends('layouts.phil_ind_admin')

@section('content')

<center>
	<h3>{{ $title }} :</h3>

@if($items)
@foreach($items as $item)
@if($item[0]->link)
<a href="{{ $item[0]->link }}">{{ $item[0]->name }}</a><br>
@else
<h5>There is nothing</h5>
@endif
@endforeach
@else
<h5>There is nothing</h5>
@endif

</center>

@endsection




