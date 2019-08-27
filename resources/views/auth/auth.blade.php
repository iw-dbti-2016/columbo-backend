@extends('layouts.app')

@section('content')
	<router-view action="{{ route('login') }}" source="{{ asset('img/undraw/') }}"></router-view>
{{--     <auth-image source="{{ asset('img/undraw/') }}" :image="formImage.source" :alt="formImage.alt"></auth-image>
    <login-form v-on:set-form="setForm"  action="{{ route('login') }}" v-show="form === 'login'"></login-form>
    <register-form v-on:set-form="setForm" action="{{ route('register') }}" v-show="form === 'register'"></register-form>
    <forgot-password-form v-on:set-form="setForm"  v-show="form === 'forgot-password'"></forgot-password-form>
 --}}
@endsection
