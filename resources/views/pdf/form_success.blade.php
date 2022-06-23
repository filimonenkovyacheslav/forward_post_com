@extends('layouts.front_signature_form')

@section('content')

<center>

@if (isset($_GET['new_document_id']))
<div class="alert alert-success  alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">×</button>  
    <strong>File {{ $_GET['pdf_file'] }} signed and saved successfully</strong>
    <a href="{{ url('/download-pdf/'.$_GET['new_document_id']) }}">Download PDF</a>
    @if (isset($_GET['old_file']))
    <strong>Old document file {{ $_GET['old_file'] }}</strong>
    @endif
</div>
@endif

@if (isset($_GET['type'])) 

@php
$type = $_GET['type'];
@endphp

@if(Auth::user())
@if(Auth::user()->role === 'office_1' || Auth::user()->role === 'admin' || Auth::user()->role === 'office_eng' || Auth::user()->role === 'office_ru')
<hr>
@if($type === 'eng_draft_id')
<a class="btn btn-success" href="{{ url('/admin/courier-eng-draft-worksheet') }}">To Admin Panel</a>
@elseif($type === 'draft_id')
<a class="btn btn-success" href="{{ url('/admin/courier-draft-worksheet') }}">To Admin Panel</a>
@elseif($type === 'worksheet_id')
<a class="btn btn-success" href="{{ url('/admin/new-worksheet') }}">To Admin Panel</a>
@else
<a class="btn btn-success" href="{{ url('/admin/phil-ind-worksheet') }}">To Admin Panel</a>
@endif
@endif
@endif  
@endif

</center>

@endsection




