<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'steps_count'  => $this->whenCounted('steps'),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
