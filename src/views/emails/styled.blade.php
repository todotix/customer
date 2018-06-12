@extends('master::layouts/email')

@section('icon')
{{ $icon }}
@endsection

@section('content')
	<h2 style="font-family: Arial, Helvetica, sans-serif;margin-top: 16px;margin-bottom: 8px;word-break: break-word;font-size: 28px;line-height: 38px;font-weight: bold;">
		{{ $title }}
	</h2>
	<p style="font-family: Arial, Helvetica, sans-serif;margin-top: 0px;margin-bottom: 20px;word-break: break-word;font-size: 19px;line-height: 31px;">
		{!! $content !!}
	</p>
	@if($link)
	  <p style="font-family: Arial, Helvetica, sans-serif;margin-top: 0px;margin-bottom: 32px;word-break: break-word;font-size: 19px;line-height: 31px;">
		<a target="_blank" href="{{ $link }}">{{ $link }}</a>
	  </p>
	@endif
@endsection

@section('unsuscribe-email')
	{{ url('auth/unsuscribe/'.urlencode($email)) }}
@endsection