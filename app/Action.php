<?php

namespace Columbo;

use Illuminate\Database\Eloquent\Model;
use Columbo\User;

class Action extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function subject()
    {
    	return $this->morphTo('actionable');
    }
}
