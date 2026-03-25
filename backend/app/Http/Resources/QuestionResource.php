<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'question_text' => $this->question_text,
            'order'         => $this->order,
            'riasec_profile'=> $this->whenLoaded('riasecProfile', fn () => [
                'id'   => $this->riasecProfile->id,
                'code' => $this->riasecProfile->code,
                'name' => $this->riasecProfile->name,
            ]),
            'answers'       => $this->whenLoaded('answers', fn () => $this->answers->map(fn($a) => [
                'id'          => $a->id,
                'answer_text' => $a->answer_text,
                'score'       => $a->score,
            ])),
        ];
    }
}
