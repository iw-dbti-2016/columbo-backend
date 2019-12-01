<?php

namespace TravelCompanion;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Action;
use TravelCompanion\Plan;
use TravelCompanion\Section;

class Location extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
    	'coordinates',
    ];

    public function sections()
    {
        return $this->morphMany(Section::class, 'locationable');
    }

    public function actions()
    {
    	return $this->morphMany(Action::class, 'actionable');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
