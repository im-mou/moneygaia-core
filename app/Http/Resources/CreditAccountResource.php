<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CreditAccountResource extends JsonResource
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
            "balance" => $this->balance,
            "active" => $this->active,
            'created_at' => $this->created_at,
            'credit_account_type' => new CreditAccountTypeResource($this->credit_account_type)
        ];
    }
}
