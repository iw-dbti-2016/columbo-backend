<?php

namespace Columbo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Columbo\Action;
use Columbo\Section;
use Columbo\Traits\Visibility;
use Columbo\Trip;
use Columbo\User;

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
