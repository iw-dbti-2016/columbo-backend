<?php

namespace Columbo;

use Columbo\Section;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POI extends Model
{
	use SpatialTrait, SoftDeletes;

	protected $table = "pois";

	protected $fillable = [
		"uuid",
		"coordinates",
		"name",
		"map_zoom",
	];

	protected $spatialFields = [
		'coordinates',
	];

	public function setCoordinatesAttribute($value) {
		$this->attributes["coordinates"] = new Point($value["latitude"], $value["longitude"]);
	}

	public function getCoordinatesAttribute($coordinates) {
		return ["latitude" => $coordinates->getLat(), "longitude" => $coordinates->getLng()];
	}

    public function sections()
    {
        return $this->morphMany(Section::class, 'locationable');
    }
}
