<?php

namespace Columbo;

use Columbo\Action;
use Columbo\Report;
use Columbo\Section;
use Columbo\Traits\Visibility;
use Columbo\TripUserRoleMember;
use Columbo\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes, Visibility;

    protected $fillable = [
        "name",
        "synopsis",
        "description",
        "start_date",
        "end_date",
        "visibility",
        "published_at",
    ];

    protected $casts = [
		"start_date"   => "date",
		"end_date"     => "date",
		"published_at" => "datetime",
    ];

    public function owner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function reports()
    {
    	return $this->hasMany(Report::class);
    }

    public function members()
    {
    	return $this->belongsToMany(User::class, TripUserRoleMember::class)
    				->using(TripUserRoleMember::class);
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
