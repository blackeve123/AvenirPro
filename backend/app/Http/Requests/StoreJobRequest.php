<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'  => 'nullable|integer|exists:job_categories,id',
            'category'     => 'nullable|string|max:255',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'sector'       => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'riasec_types' => 'nullable|string|max:100',
            'image'        => 'nullable|image|mimes:jpg,png,webp|max:2048',
        ];
    }
}
