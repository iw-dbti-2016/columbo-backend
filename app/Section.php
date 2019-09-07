<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Action;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Traits\Visibility;
use TravelCompanion\Trip;
use TravelCompanion\User;

class Section extends Model
{
    use Visibility;

    protected $fillable = [
        "content",
        "visibility",
        "published_at",
    ];

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

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
