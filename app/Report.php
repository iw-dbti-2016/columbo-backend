<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Action;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class Report extends Model
{
    public function owner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function trip()
    {
    	return $this->belongsTo(Trip::class);
    }

    public function sections()
    {
    	return $this->hasMany(Section::class);
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
