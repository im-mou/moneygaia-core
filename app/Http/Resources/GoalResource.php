<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class GoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => Str::title($this->title),
            "description" => $this->description,
            "ammount" => $this->ammount,
            "due_date" => $this->due_date,
            "achived" => $this->achived,
            "created_at" => $this->created_at,
            "icon" => new IconResource($this->icon),
        ];
    }
}
