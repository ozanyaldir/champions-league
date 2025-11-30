<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,

            'played' => $this->whenNotNull($this->played),
            'won' => $this->whenNotNull($this->won),
            'draw' => $this->whenNotNull($this->draw),
            'lost' => $this->whenNotNull($this->lost),
            'points' => $this->whenNotNull($this->points),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
