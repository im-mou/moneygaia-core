<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BudgetResource extends JsonResource
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
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            'created_at' => $this->created_at,
            "transaction_type" => new TransactionTypeResource($this->transaction_type),
        ];
    }
}
