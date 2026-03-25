<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\Question;
use App\Http\Resources\QuestionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    // ─── GET /questions ───────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'questions_all';
        $questions = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return Question::with(['riasecProfile', 'answers'])
                ->orderBy('order')
                ->get();
        });

        return $this->successResponse(QuestionResource::collection($questions));
    }
}
