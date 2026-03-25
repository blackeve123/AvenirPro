<?php

namespace App\Services;

use App\Models\Job;

class RiasecScoringService
{
    /**
     * Calculate RIASEC scores, determine top profile, and match jobs.
     */
    public function calculateAndMatchJobs(iterable $userAnswers): array
    {
        // Accumulate scores per RIASEC code
        $scores = ['R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0];

        foreach ($userAnswers as $ua) {
            $code = $ua->question?->riasecProfile?->code;
            if ($code && isset($scores[$code])) {
                $scores[$code] += $ua->answer?->score ?? 0;
            }
        }

        // Build top profile string (top 3 codes sorted by score)
        arsort($scores);
        $topCodes    = array_keys(array_slice($scores, 0, 3, true));
        $topProfile  = implode('', $topCodes); // e.g. "IAE"

        $totalTopScore = 0;
        foreach ($topCodes as $c) { $totalTopScore += $scores[$c]; }
        $totalTopScore = $totalTopScore ?: 1;

        // Match jobs and compute compatibility scores
        $allJobs = Job::with('category')->get();

        // Score all jobs first
        $allScored = $allJobs
            ->shuffle()
            ->map(function (Job $job) use ($scores, $topCodes, $totalTopScore) {
                $rawTypes = $job->riasec_types;
                if (is_string($rawTypes)) {
                    $decoded = json_decode($rawTypes, true);
                    if (is_array($decoded)) {
                        $jobTypes = $decoded;
                    } else {
                        $jobTypes = array_filter(array_map('trim', explode(',', $rawTypes)));
                    }
                } else {
                    $jobTypes = is_array($rawTypes) ? $rawTypes : [];
                }

                if (empty($jobTypes)) return null;

                // ── A. Overlap Weight (50%) ───────────────────────────────────
                $intersect    = array_intersect($jobTypes, $topCodes);
                $overlapRatio = count($intersect) / count($jobTypes);

                // ── B. Intensity Weight (50%) ─────────────────────────────────
                $jobScoreSum = 0;
                foreach ($jobTypes as $t) { $jobScoreSum += $scores[$t] ?? 0; }
                $intensityRatio = $jobScoreSum / $totalTopScore;

                // ── Final Calculation ─────────────────────────────────────────
                $percentage = ($overlapRatio * 50) + ($intensityRatio * 50);

                // Penalty if the primary job type is NOT in user's top 3
                if (!empty($jobTypes) && !in_array($jobTypes[0], $topCodes)) {
                    $percentage *= 0.85;
                }

                if (count($intersect) === count($jobTypes)) {
                    $percentage *= 1.1;
                }

                $percentage = min(100.0, $percentage);

                return [
                    'id'          => $job->id,
                    'title'       => $job->title,
                    'sector'      => $job->sector,
                    'category'    => $job->category?->name,
                    'image_url'   => $job->image_url,
                    'description' => $job->description,
                    'raw_score'   => $percentage,
                    'match_pct'   => (int) round($percentage),
                    'reason'      => '',
                ];
            })
            ->filter()
            ->sortByDesc('raw_score')
            ->values();

        // ── Category-Diversity Selection ──────────────────────────────────────
        // Pick top 2 jobs per category, then sort by score and take 6.
        // This prevents a single category from monopolising all slots.
        $selectedByCategory = collect();
        $categoryCounts     = [];
        foreach ($allScored as $job) {
            $cat = $job['category'] ?? 'Autre';
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0);
            if ($categoryCounts[$cat] < 2) {
                $selectedByCategory->push($job);
                $categoryCounts[$cat]++;
            }
            if ($selectedByCategory->count() >= 12) break; // candidate pool
        }

        $matchedJobs = $selectedByCategory
            ->sortByDesc('raw_score')
            ->values()
            ->take(6)
            ->toArray();

        // If fewer than 3 matches, ensure at least 3 for UI flow
        if (count($matchedJobs) < 3) {
            $existingIds = array_column($matchedJobs, 'id');
            $fallbacks = $allJobs->whereNotIn('id', $existingIds)->shuffle()->take(3 - count($matchedJobs));
            foreach ($fallbacks as $job) {
                $matchedJobs[] = [
                    'id'          => $job->id,
                    'title'       => $job->title,
                    'sector'      => $job->sector,
                    'category'    => $job->category?->name,
                    'image_url'   => $job->image_url,
                    'description' => $job->description,
                    'match_pct'   => 35, // Generic match
                    'reason'      => '',
                ];
            }
        }

        return [
            'scores'      => $scores,
            'topProfile'  => $topProfile,
            'matchedJobs' => $matchedJobs,
        ];

    }
}
