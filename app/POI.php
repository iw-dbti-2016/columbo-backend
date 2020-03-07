<?php

namespace Columbo;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POI extends Model
{
	use SpatialTrait, SoftDeletes;

	protected $table = "pois";

	protected $spatialFields = [
		'coordinates',
	];

	public function getCoordinatesAttribute($coordinates) {
		return [$coordinates->getLat(), $coordinates->getLng()];
	}
}
