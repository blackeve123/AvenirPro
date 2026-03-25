<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\Answer;
use App\Models\Job;
use App\Models\TestResult;
use App\Models\UserAnswer;
use App\Http\Resources\TestResultResource;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\CalculateTestRequest;
use Illuminate\Http\Request;

class TestController extends Controller
{
    // ─── POST /test/answer ────────────────────────────────────────────────────
    /**
     * Store or update a single user answer.
     * Body: { question_id: int, answer_id: int }
     */
    public function storeAnswer(StoreAnswerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Verify the answer belongs to the question
        $answer = Answer::where('id', $validated['answer_id'])
            ->where('question_id', $validated['question_id'])
            ->firstOrFail();

        // Upsert — one answer per question per user
        UserAnswer::updateOrCreate(
            [
                'user_id'     => $request->user()->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'answer_id' => $answer->id,
            ]
        );

        return $this->successResponse(null, 'Answer saved.');
    }

    // ─── POST /test/calculate ─────────────────────────────────────────────────
    /**
     * Compute the RIASEC profile from all stored answers,
     * match compatible jobs, enrich with Gemini AI, and save the result.
     */
    public function calculate(CalculateTestRequest $request): JsonResponse
    {
        $user = $request->user();

        // ── Step 1: Load user answers ─────────────────────────────────────────
        $userAnswers = UserAnswer::where('user_id', $user->id)
            ->with(['answer', 'question.riasecProfile'])
            ->get();

        if ($userAnswers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No answers found. Please complete the test first.',
            ], 422);
        }

        // ── Step 2-5: Calculate scores and compute compatible jobs ────────────
        $scoringService = new \App\Services\RiasecScoringService();
        $scoringResult = $scoringService->calculateAndMatchJobs($userAnswers);

        $scores      = $scoringResult['scores'];
        $topProfile  = $scoringResult['topProfile'];
        $matchedJobs = $scoringResult['matchedJobs'];

        // ── Step 6: Enrich with Gemini AI ────────────────────────────────────
        $gemini      = new GeminiService();
        $enrichedJobs = $gemini->enrich($topProfile, $scores, $matchedJobs);

        // ── Step 7: Persist result ────────────────────────────────────────────
        $result = TestResult::updateOrCreate(
            ['user_id' => $user->id],
            [
                'riasec_scores'    => $scores,
                'top_profile'      => $topProfile,
                'recommended_jobs' => $enrichedJobs,
            ]
        );

        Log::info("Test calculated for user: {$user->email}", [
            'top_profile' => $topProfile,
            'scores'      => $scores,
            'jobs_count'  => count($enrichedJobs),
        ]);

        return $this->successResponse(new TestResultResource($result));
    }

    // ─── GET /my-results ──────────────────────────────────────────────────────

    public function myResults(Request $request): JsonResponse
    {
        $result = TestResult::where('user_id', $request->user()->id)
            ->latest()
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'No test results found. Please take the orientation test.',
            ], 404);
        }

        return $this->successResponse(new TestResultResource($result));
    }

    /**
     * Get the list of question IDs already answered by the user.
     */
    public function status(Request $request): JsonResponse
    {
        $answeredIds = UserAnswer::where('user_id', $request->user()->id)
            ->pluck('question_id');

        return $this->successResponse([
            'answered_ids' => $answeredIds,
            'count'        => count($answeredIds)
        ]);
    }

    /**
     * Reset the user's test progress by deleting all answers and results.
     */
    public function resetTest(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        
        UserAnswer::where('user_id', $userId)->delete();
        TestResult::where('user_id', $userId)->delete();

        return $this->successResponse(null, 'Test reset successfully.');
    }
}

