@extends('layouts.app')

@section('content')
    <div class="flex h-full max-w-md mx-auto mx-4 antialiased">
        <div class="w-full my-auto bg-gray-100 text-gray-900 rounded-lg px-6 py-4 shadow-lg">
            <div class="text-3xl font-semibold text-center">Verify Your Email Address</div>

            <div>
                @if (session('resent'))
                    <div class="bg-green-200 text-center py-4 text-md rounded mt-4" role="alert">
                        A fresh verification link has been sent to your email address.
                    </div>
                @endif

                <p class="mt-4">Before proceeding, please check your email for a verification link.</p>
                <p class="mt-2">If you did not receive the email, <a class="underline" href="{{ route('verification.resend') }}">click here to request another</a>.</p>
            </div>
        </div>
    </div>
@endsection
