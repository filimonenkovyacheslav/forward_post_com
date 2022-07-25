@foreach($items as $item)
<form action="{{ route('downloadDirectory') }}" method="POST">
	@csrf
	<input type="hidden" name="path" value="{{ (is_object($item)) ? $item->path : $item['path'] }}">
	<button type="submit">{{ (is_object($item)) ? $item->name : $item['name'] }}</button>
</form>
@endforeach