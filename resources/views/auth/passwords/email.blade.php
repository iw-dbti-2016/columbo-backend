@extends('layouts.app')

@section('content')
    <div class="flex h-full max-w-md mx-auto mx-4 antialiased">
        <div class="w-full my-auto bg-gray-100 text-gray-900 rounded-lg px-6 py-4 shadow-lg">
            <div class="text-4xl font-semibold text-center">Reset Password</div>

            <div>
                @if (session('status'))
                    <div class="bg-green-200 text-center py-4 text-md rounded mt-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mt-4">
                        <label for="email">E-Mail Address</label>

                        <input id="email" type="email" class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="text-red-600" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="block shadow block w-full mt-4 bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:outline-none text-white px-4 py-3 rounded-lg uppercase text-sm font-semibold tracking-wider" type="submit">
                        {{ __('Send Password Reset Link') }}
                    </button>
                    <a href="{{ route('login') }}" class="inline-block mt-2 text-gray-600 hover:underline">Back to Login</a>
                </form>
            </div>
        </div>
    </div>
@endsection
