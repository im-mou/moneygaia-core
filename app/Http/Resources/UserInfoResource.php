<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserInfoResource extends JsonResource
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
            "total_balance" => $this->total_balance,
            "currency" => $this->currency,
            "country" => $this->country,
            "language" => $this->language,
            "date_format" => $this->date_format,
        ];
    }
}
