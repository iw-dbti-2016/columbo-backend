<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Action;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\User;

class Trip extends Model
{
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
    	return $this->belongsToMany(User::class, 'trip_user_role_members');
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
