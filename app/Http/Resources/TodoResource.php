<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'completed' => $this->completed,
            'person_id' => $this->person_id,
            'person' => $this->when($this->person, new PersonResource($this->person)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
