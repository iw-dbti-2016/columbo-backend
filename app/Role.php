<?php

namespace Columbo;

use Illuminate\Database\Eloquent\Model;
use Columbo\Permission;
use Columbo\Trip;

class Role extends Model
{
	protected $primaryKey = "label";

	protected $keyType = "string";

	public $incrementing = false;

	public function permissions()
	{
		return $this->belongsToMany(Permission::class);
	}
}
