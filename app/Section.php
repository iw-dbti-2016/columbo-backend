<?php

namespace TravelCompanion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use TravelCompanion\Action;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Traits\Visibility;
use TravelCompanion\Trip;
use TravelCompanion\User;

class Section extends Model
{
    use SoftDeletes, Visibility;

    protected $fillable = [
        "content",
        "image",
        "start_time",
        "end_time",
        "is_draft",
        "visibility",
        "published_at",
    ];

    protected $visible = [
        "id",
        "is_draft",
        "content",
        "image",
        "start_time",
        "end_time",
        "visibility", // Sometimes???
        "published_at",
        "deleted_at",
        "created_at",
        "updated_at",
        "published_at_diff",
        "locationable",
        "owner",
    ];

    protected $casts = [
        "published_at" => "datetime:d/m/Y H:i",
        "is_draft" => "boolean",
    ];

    protected $appends = [
        "published_at_diff",
        "duration_formatted",
    ];

    public function scopeNoDraft($query)
    {
        return $query->whereIsDraft(false);
    }

    public function scopeOrderRecent($query)
    {
        return $query->orderBy("published_at", "desc");
    }

    public function scopePublished($query)
    {
        return $query->where("published_at", "<", Carbon::now("UTC"));
    }

    public function getStartTimeAttribute($start_time)
    {
        return ($start_time == null) ? $start_time : substr($start_time, 0, 5);
    }

    public function getEndTimeAttribute($end_time)
    {
        return ($end_time == null) ? $end_time : substr($end_time, 0, 5);
    }

    public function getPublishedAtDiffAttribute()
    {
        return Carbon::parse($this->published_at)->diffForHumans();
    }

    public function owner()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function report()
    {
    	return $this->belongsTo(Report::class);
    }

    public function locationable()
    {
    	return $this->morphTo('locationable');
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
