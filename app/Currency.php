<?php

namespace Columbo;

use Illuminate\Database\Eloquent\Model;
use Columbo\User;

class Currency extends Model
{
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = "string";

    public function users()
    {
    	return $this->hasMany(User::class, 'currency_preference', 'id');
    }
}
