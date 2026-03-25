<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\Job;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobDetailResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    // ─── GET /jobs ────────────────────────────────────────────────────────────

    public function index(Request $request): AnonymousResourceCollection
    {
        $cacheKey = 'jobs_all_' . md5(json_encode($request->all()));

        $jobs = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($request) {
            $query = Job::with('category');

            if ($request->filled('category')) {
                $cat = $request->category;
                if (is_numeric($cat)) {
                    $query->where('category_id', $cat);
                } else {
                    $query->whereHas('category', function ($q) use ($cat) {
                        $q->where('name', 'LIKE', "%{$cat}%");
                    });
                }
            }

            if ($request->filled('search')) {
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            }

            return $query->paginate(12);
        });

        // Use direct Resource collection to keep Laravel's pagination meta
        return JobResource::collection($jobs);
    }

    // ─── GET /categories ──────────────────────────────────────────────────────

    public function categories(): JsonResponse
    {
        $categories = \App\Models\JobCategory::select('id', 'name')->orderBy('name')->get();
        return $this->successResponse($categories);
    }

    // ─── GET /jobs/{id} ───────────────────────────────────────────────────────

    public function show(int $id): JsonResponse
    {
        $job = Job::with(['category', 'steps'])->findOrFail($id);

        return $this->successResponse(new JobDetailResource($job));
    }

    // ─── GET /jobs/{id}/steps ─────────────────────────────────────────────────

    public function steps(int $id): JsonResponse
    {
        $job = Job::findOrFail($id);
        $steps = $job->steps()->orderBy('order')->get();

        return $this->successResponse([
            'job' => new JobResource($job),
            'steps' => $steps,
        ]);
    }
}
