<?php

namespace TravelCompanion;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Section;

class Location extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
    	'coordinates',
    ];

    public function sections()
    {
    	return $this->belongsToMany(Section::class);
    }
}
