@extends('layouts.app')

@section('content')
    <div class="relative flex h-full">
        <img class="max-w-6xl my-auto mx-auto" src="{{ asset('img/undraw/outer_space.png') }}" alt="Outer Space">
    </div>
    <h1 class="absolute w-full text-center top-0 mt-10 left-0 text-4xl">We're working here, check back later!</h1>
    <div class="absolute top-0 right-0 container text-right px-4 py-2">
        <a class="mr-4 font-semibold hover:underline" href="{{ route('login') }}">Login</a>
        <a class="font-semibold hover:underline" href="{{ route('register') }}">Sign up for the <span class="text-blue-600">β</span></a>
    </div>
@endsection
