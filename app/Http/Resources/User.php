<?php

namespace Columbo\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
        	"data" => [
        		"type" => "user",
        		"id" => $this->id,
        		"attributes" => [
        			"first_name"  => $this->first_name,
        			"middle_name" => $this->middle_name,
        			"last_name"   => $this->last_name,
        			"username"    => $this->username,
        			"email"       => $this->email,
        			"telephone"   => $this->telephone,
        			"image"       => $this->image,
        			"birth_date"  => $this->birth_date,
        			"description" => $this->description,
        			"language"    => $this->language,
        		],
        	],
        	"links" => [
        		"self" => url("/users/" . $this->id),
        	],
        ];
    }
}
