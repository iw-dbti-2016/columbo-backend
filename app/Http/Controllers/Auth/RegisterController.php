<?php

namespace TravelCompanion\Http\Controllers\Auth;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use TravelCompanion\Http\Controllers\Controller;
use TravelCompanion\User;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/app';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest', 'withoutTokenCookies']);
        $this->redirectTo = route('verification.notice');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            "first_name" => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
            "middle_name" => ["nullable", "max:100", "regex:/^((([A-Z]{1}\.)|([A-Za-z-']+)) ?)+$/"],
            "last_name" => "required|min:2|max:50|regex:/^[A-Za-z-']+$/",
            "username" => "required|min:4|max:40|regex:/^[A-Za-z0-9-.]+$/|unique:users",
            "email" => "required|max:80|email|unique:users",
            "home_location" => ["required", "regex:/^(-?([0-8][0-9]?\.[0-9]{1,8}|90\.[0]{1,8}) -?([0-9]{1,2}\.[0-9]{1,8}|1[0-7][0-9]\.[0-9]{1,8}|180\.[0]{1,8}))$/"],
            "password" => "required|min:6|confirmed",
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \TravelCompanion\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'home_location' => Point::fromString($data['home_location']),
            'password' => Hash::make($data['password']),
        ]);
    }
}
