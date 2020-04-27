<?php

namespace Columbo;

use Columbo\Action;
use Columbo\Interfaces\TrackedByActions;
use Columbo\Plan;
use Columbo\Section;
use Columbo\Traits\Visibility;
use Columbo\Trip;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model implements TrackedByActions
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

    public function setCoordinatesAttribute($value) {
        $this->attributes["coordinates"] = new Point($value[0], $value[1]);
    }

    public function getCoordinatesAttribute($coordinates) {
    	return [$coordinates->getLat(), $coordinates->getLng()];
    }

    public function trip()
    {
    	return $this->belongsTo(Trip::class);
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
