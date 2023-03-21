@extends('layouts.front_signature_form')

@section('content')

<center>

@if (isset($_GET['new_document_id']))
<div class="alert alert-success  alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">×</button>  
    <strong>Your packing list {{ $_GET['pdf_file'] }} was completed successfully. Push the button below to download it. / Ваш пакинг-лист {{ $_GET['pdf_file'] }} оформлен успешно. Чтобы скачать его, нажмите кнопку внизу</strong>   
    @if (isset($_GET['old_file']))
    <strong>Old document file / Старый документ {{ $_GET['old_file'] }}</strong>
    @endif
    <hr><a class="btn btn-success" href="{{ url('/download-pdf/'.$_GET['new_document_id']) }}">Download packing list / Скачать пакинг-лист</a>
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
<a class="btn btn-success" href="{{ url('/admin/courier-eng-draft-worksheet') }}">To Admin Panel / К Админ-панели</a>
@elseif($type === 'draft_id')
<a class="btn btn-success" href="{{ url('/admin/courier-draft-worksheet') }}">To Admin Panel / К Админ-панели</a>
@elseif($type === 'worksheet_id')
<a class="btn btn-success" href="{{ url('/admin/new-worksheet') }}">To Admin Panel / К Админ-панели</a>
@else
<a class="btn btn-success" href="{{ url('/admin/phil-ind-worksheet') }}">To Admin Panel / К Админ-панели</a>
@endif
@endif
@endif  
@endif

</center>

@endsection




