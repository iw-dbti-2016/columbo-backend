<?php

namespace Columbo;

use Columbo\Casts\ActionType;
use Columbo\User;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
	protected $fillable = [
		"uuid",
		"action",
	];

	protected $casts = [
		"action" => ActionType::class,
		"executed_at" => "datetime",
	];

	public $timestamps = false;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function subject()
    {
    	return $this->morphTo('actionable');
    }
}
