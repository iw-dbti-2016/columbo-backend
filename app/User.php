<?php

namespace Columbo;

use Columbo\Action;
use Columbo\Currency;
use Columbo\Interfaces\TrackedByActions;
use Columbo\Location;
use Columbo\Report;
use Columbo\Section;
use Columbo\Trip;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, TrackedByActions
{
    use HasApiTokens, Notifiable;

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

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
