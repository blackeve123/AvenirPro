<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'riasec_profile_id' => 'required|integer|exists:riasec_profiles,id',
            'question_text'     => 'required|string',
            'order'             => 'sometimes|integer|min:1',
        ];
    }
}
