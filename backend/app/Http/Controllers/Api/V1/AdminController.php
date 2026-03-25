<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\Job;
use App\Models\Question;
use App\Models\Step;
use App\Http\Resources\JobDetailResource;
use App\Http\Resources\QuestionResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Http\Requests\StoreQuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\JobCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests;
    // ══════════════════════════════════════════════════════════════════════════
    // JOBS
    // ══════════════════════════════════════════════════════════════════════════

    // ─── POST /admin/jobs ─────────────────────────────────────────────────────
    public function createJob(StoreJobRequest $request): JsonResponse
    {
        $this->authorize('create', Job::class);
        Cache::flush();
        $validated = $request->validated();

        // Resolve category
        if ($request->filled('category')) {
            $category = JobCategory::firstOrCreate(['name' => $request->category]);
            $validated['category_id'] = $category->id;
        }

        // Handle RIASEC types (convert string to array)
        if ($request->filled('riasec_types') && is_string($request->riasec_types)) {
            $validated['riasec_types'] = array_filter(array_map('trim', explode(',', $request->riasec_types)));
        }

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $filename = uniqid('job_') . '.webp';
            $path = 'jobs/' . $filename;

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $imageObj = $manager->read($imageFile);
            $encoded = $imageObj->toWebp(80);

            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $encoded);
            $validated['image_path'] = $path;
        }

        $job = Job::create($validated);
        $job->load('category');

        Log::info("Admin created job: {$job->id}");

        return $this->successResponse(new JobDetailResource($job), 'Job created.', 201);
    }

    // ─── PUT /admin/jobs/{id} ─────────────────────────────────────────────────
    public function updateJob(UpdateJobRequest $request, int $id): JsonResponse
    {
        $job = Job::findOrFail($id);
        $this->authorize('update', $job);

        Cache::flush();
        $validated = $request->validated();

        // Resolve category
        if ($request->filled('category')) {
            $category = JobCategory::firstOrCreate(['name' => $request->category]);
            $validated['category_id'] = $category->id;
        }

        // Handle RIASEC types (convert string to array)
        if ($request->filled('riasec_types') && is_string($request->riasec_types)) {
            $validated['riasec_types'] = array_filter(array_map('trim', explode(',', $request->riasec_types)));
        }

        if ($request->hasFile('image')) {
            if ($job->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($job->image_path);
            }
            
            $imageFile = $request->file('image');
            $filename = uniqid('job_') . '.webp';
            $path = 'jobs/' . $filename;

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $imageObj = $manager->read($imageFile);
            $encoded = $imageObj->toWebp(80);

            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $encoded);
            $validated['image_path'] = $path;
        }

        $job->update($validated);

        Log::info("Admin updated job: {$job->id}");

        return $this->successResponse(new JobDetailResource($job->fresh('category')), 'Job updated.');
    }

    // ─── DELETE /admin/jobs/{id} ──────────────────────────────────────────────
    public function deleteJob(int $id): JsonResponse
    {
        Cache::flush();
        Job::findOrFail($id)->delete();

        Log::info("Admin deleted job: {$id}");

        return $this->successResponse(null, 'Job deleted.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STEPS
    // ══════════════════════════════════════════════════════════════════════════

    // ─── POST /admin/jobs/{jobId}/steps ───────────────────────────────────────
    public function createStep(Request $request, int $jobId): JsonResponse
    {
        $job = Job::findOrFail($jobId);
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'sometimes|integer|min:1',
            'duration' => 'nullable|string|max:100',
        ]);

        $step = Step::create(array_merge($validated, ['job_id' => $jobId]));

        return $this->successResponse($step, 'Step created.', 201);
    }

    // ─── DELETE /admin/steps/{id} ─────────────────────────────────────────────
    public function deleteStep(int $id): JsonResponse
    {
        $step = Step::findOrFail($id);
        $this->authorize('delete', $step);
        $step->delete();

        return $this->successResponse(null, 'Step deleted.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // QUESTIONS
    // ══════════════════════════════════════════════════════════════════════════

    // ─── POST /admin/questions ────────────────────────────────────────────────
    public function createQuestion(StoreQuestionRequest $request): JsonResponse
    {
        $this->authorize('create', Question::class);
        Cache::flush();
        $validated = $request->validated();

        $question = Question::create($validated);
        $question->load('riasecProfile');

        return $this->successResponse(new QuestionResource($question), 'Question created.', 201);
    }

    // ─── DELETE /admin/questions/{id} ─────────────────────────────────────────
    public function deleteQuestion(int $id): JsonResponse
    {
        Cache::flush();
        $question = Question::findOrFail($id);
        $this->authorize('delete', $question);
        $question->delete();

        return $this->successResponse(null, 'Question deleted.');
    }

    // ─── PUT /admin/questions/{id} ──────────────────────────────────────────
    public function updateQuestion(StoreQuestionRequest $request, int $id): JsonResponse
    {
        Cache::flush();
        $question = Question::findOrFail($id);
        $question->update($request->validated());

        return $this->successResponse(new QuestionResource($question->load('riasecProfile')), 'Question updated.');
    }
}
