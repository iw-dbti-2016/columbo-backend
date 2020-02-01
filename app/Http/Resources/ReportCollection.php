<?php

namespace TravelCompanion\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
        	"data" => $this->collection,
        	"links" => [
        		"self" => url("/reports"),
        	],
        ];
    }
}
