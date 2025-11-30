<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'week' => $this->week,
            'home' => $this->homeTeam,
            'away' => $this->awayTeam,
        ];
    }
}
