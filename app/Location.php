<?php

namespace TravelCompanion;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use TravelCompanion\Action;
use TravelCompanion\Section;

class Location extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
    	'coordinates',
    ];

    public function sections()
    {
        return $this->morphToMany(
                        Section::class,
                        'locationable',
                        'section_locationables'
                    );
    }

    public function actions()
    {
    	return $this->morphMany(Action::class, 'actionable');
    }
}
