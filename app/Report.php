<?php

namespace Columbo;

use Columbo\Action;
use Columbo\Interfaces\TrackedByActions;
use Columbo\Section;
use Columbo\Traits\Visibility;
use Columbo\Trip;
use Columbo\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model implements TrackedByActions
{
    use SoftDeletes, Visibility;

    protected $fillable = [
        "title",
        "date",
        "description",
        "is_locked",
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
