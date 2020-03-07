<?php

namespace TravelCompanion;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TravelCompanion\Action;
use TravelCompanion\Plan;
use TravelCompanion\Section;
use TravelCompanion\Traits\Visibility;

class Location extends Model
{
    use SpatialTrait, Visibility, SoftDeletes;

    protected $fillable = [
        "is_draft",
        "coordinates",
        "map_zoom",
        "name",
        "info",
        "visibility",
        "published_at",
    ];

    protected $casts = [
    	"is_draft" => "boolean",
    ];

    protected $spatialFields = [
    	'coordinates',
    ];

    // public function setCoordinatesAttribute($value) {
    //     $this->coordinates = new Point($value[0], $value[1]);
    // }

    public function getCoordinatesAttribute($coordinates) {
    	return [$coordinates->getLat(), $coordinates->getLng()];
    }

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
