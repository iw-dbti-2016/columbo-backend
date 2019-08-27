@extends('layouts.app')

@section('content')
    <div class="flex h-full max-w-md mx-auto mx-4 antialiased">
        <div class="w-full my-auto bg-gray-100 text-gray-900 rounded-lg px-6 py-4 shadow-lg">
            <div class="text-4xl font-semibold text-center">Reset Password</div>

            <div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mt-4">
                        <label for="email">E-Mail Address</label>
                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="text-red-600" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <label for="password">Password</label>
                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="password" type="password" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="text-red-600" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <label for="password-confirm">Confirm Password</label>
                        <input class="shadow focus:shadow-lg mt-1 outline-none px-4 py-3 rounded-lg w-full" id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <button class="block shadow block w-full mt-4 bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:outline-none text-white px-4 py-3 rounded-lg uppercase text-sm font-semibold tracking-wider" type="submit">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
