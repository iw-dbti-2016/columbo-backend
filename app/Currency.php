<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use TravelCompanion\User;

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
