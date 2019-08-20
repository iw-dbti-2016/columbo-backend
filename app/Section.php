<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\User;

class Section extends Model
{
    public function owner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function report()
    {
    	return $this->belongsTo(Report::class);
    }

    public function locations()
    {
    	return $this->belongsToMany(Location::class);
    }
}
