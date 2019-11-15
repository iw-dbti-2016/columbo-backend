<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TravelCompanion\Action;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Traits\Visibility;
use TravelCompanion\Trip;
use TravelCompanion\User;

class Section extends Model
{
    use SoftDeletes, Visibility;

    protected $fillable = [
        "content",
        "image",
        "time",
        "duration_minutes",
        "is_draft",
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

    public function location()
    {
    	return $this->morphTo('locationable');
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
