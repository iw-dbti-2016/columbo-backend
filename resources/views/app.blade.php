<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="base-url" content="{{ url('/') }}">

	<title>{{ config('app.name', 'Laravel') }}</title>

	<!-- Styles -->
	<link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="h-screen antialiased leading-none font-sans bg-gray-900">
<div class="flex flex-col">
	@if(Route::has('login'))
		<div class="absolute top-0 right-0 mt-2 mr-6">
			<form id="logout" action="{{ route('logout') }}" method="post">
				@csrf
				<input class="focus:outline-none focus:font-semibold border-none bg-transparent py-2 cursor-pointer hover:underline" type="submit" value="Log out">
			</form>
		</div>
	@endif
	<div id="app">

	</div>
</div>
</body>
<script src="{{ mix('js/app.js') }}"></script>
</html>
