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
        "time",
        "duration_minutes",
        "is_draft",
        "visibility",
        "published_at",
    ];

    protected $casts = [
        "published_at" => "datetime:d/m/Y H:i:s",
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

    public function getDurationFormattedAttribute()
    {
        if ($this->duration_minutes == null) {
            return $this->duration_minutes;
        } else {
            $hr = floor($this->duration_minutes / 60);
            $min = $this->duration_minutes % 60;

            $formatted_duration = "";

            if ($hr != 0) {
                $formatted_duration .= $hr . "h";
            }

            if ($min != 0) {
                $formatted_duration .= $min;

                if ($hr == 0) $formatted_duration .= "m";
            }

            return $formatted_duration;
        }
    }

    public function getTimeAttribute($time)
    {
        return ($time == null) ? $time : substr($time, 0, 5);
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

    public function location()
    {
    	return $this->morphTo('locationable');
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
