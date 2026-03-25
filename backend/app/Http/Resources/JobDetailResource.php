<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'sector'       => $this->sector,
            'salary_range' => $this->salary_range,
            'riasec_code'  => $this->riasec_types,
            'category'     => $this->whenLoaded('category', fn () => $this->category->name),
            'image_url'    => $this->image_url,
            'steps'        => $this->whenLoaded('steps', fn () => $this->steps->map(fn($s) => [
                'id'          => $s->id,
                'title'       => $s->title,
                'description' => $s->description,
                'order'       => $s->order,
                'duration'    => $s->duration,
            ])),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
