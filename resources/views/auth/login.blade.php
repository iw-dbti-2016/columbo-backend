@extends('layouts.app')

@section('content')
<div class="flex h-full max-w-4xl mx-auto mx-4 antialiased">
    <div class="my-auto hidden lg:block w-1/2">
        <h1 class="text-2xl text-center">Go Forward<br><span class="text-blue-600 font-semibold">Don't look back!</span></h1>
        <img src="{{ asset('img/undraw/travelers.png') }}" alt="Travelers">
    </div>

    <div class="my-auto lg:ml-8 bg-gray-100 text-gray-900 md:w-1/2 mx-auto rounded-lg px-6 py-4 shadow-lg">
        <h1 class="text-center font-semibold text-4xl">TravelCompanion</h1>
        <div class="mt-4 font-sans font-semibold text-lg">{{ __('Login') }}</div>

        <div class="mt-2">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="email">{{ __('Username') }}</label>

                    <div>
                        <input class="focus:shadow mt-1 outline-none px-4 py-3 rounded-lg w-full" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="text-red-600" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-2">
                    <label for="password">{{ __('Password') }}</label>

                    <div>
                        <input class="focus:shadow mt-1 outline-none px-4 py-3 rounded-lg w-full" id="password" type="password" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="text-red-600" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-2">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <div>
                    <button class="block w-full mt-2 bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:outline-none text-white px-4 py-3 rounded-lg uppercase text-sm font-semibold tracking-wider" type="submit">
                        {{ __('Login') }}
                    </button>

                    <div class="mt-2 flex text-gray-600 align-baseline justify-between">
                        <a class="inline-block hover:underline" href="{{ route('register') }}">Register</a>
                        @if (Route::has('password.request'))
                            <a class="inline-block hover:underline" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection