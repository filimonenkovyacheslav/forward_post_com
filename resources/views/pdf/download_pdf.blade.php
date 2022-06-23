@foreach($items as $item)
<form action="{{ route('downloadDirectory') }}" method="POST">
	@csrf
	<input type="hidden" name="path" value="{{$item['path']}}">
	<button type="submit">{{$item['name']}}</button>
</form>
@endforeach