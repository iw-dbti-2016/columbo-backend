<?php

namespace TravelCompanion;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TravelCompanion\Action;
use TravelCompanion\Currency;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    use SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'email',
        'home_location',
        'password',
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

    /**
     * Get the ientifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

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

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
