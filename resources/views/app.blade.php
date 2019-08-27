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
<body class="bg-gray-100 h-screen antialiased leading-none">
<div class="flex flex-col">
    @if(Route::has('login'))
        <div class="absolute top-0 right-0 mt-2 mr-6">
            <form id="logout" action="{{ route('logout') }}" method="post">
                @csrf
                <input class="focus:outline-none focus:font-semibold border-none bg-transparent py-2 cursor-pointer hover:underline" type="submit" value="Log out">
            </form>
        </div>
    @endif

    <div class="min-h-screen flex items-center justify-center">
        <div class="flex flex-col justify-around h-full">
            <div id="app">
                <h1 class="text-gray-600 text-center font-light tracking-wider text-5xl mb-6">
                    This is the home of <span class="font-semibold">{{ config('app.name', 'Laravel') }}</span>
                </h1>
                <a @click.prevent="getUserData" href="#">Get your data</a>
                <div v-show="userData !== {}">
                    <ul>
                        <li v-for="(item,key) in userData">@{{ key }}: @{{ item }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script src="{{ mix('js/app.js') }}"></script>
</html>
