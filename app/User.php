<?php

namespace TravelCompanion;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use TravelCompanion\Currency;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;

class User extends Authenticatable
{
    use Notifiable;
    use SpatialTrait;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $spatialFields = [
        'home_location',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_preference');
    }

    public function tripsOwner()
    {
        return $this->hasMany(Trip::class, 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function tripsMember()
    {
        return $this->belongsToMany(Trip::class, 'trip_user_role_members');
    }
}
