<?php

namespace Columbo;

use Illuminate\Database\Eloquent\Model;
use Columbo\Role;

class Permission extends Model
{
	protected $primaryKey =  "label";

	protected $keyType = "string";

	public $incrementing = false;

	public function roles()
	{
		return $this->belongsToMany(Role::class);
	}
}
