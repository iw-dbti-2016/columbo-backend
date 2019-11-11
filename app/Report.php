<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TravelCompanion\Action;
use TravelCompanion\Section;
use TravelCompanion\Traits\Visibility;
use TravelCompanion\Trip;
use TravelCompanion\User;

class Report extends Model
{
    use SoftDeletes, Visibility;

    protected $fillable = [
        "title",
        "date",
        "description",
        "visibility",
        "published_at",
    ];

    public function owner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function trip()
    {
    	return $this->belongsTo(Trip::class);
    }

    public function sections()
    {
    	return $this->hasMany(Section::class);
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
