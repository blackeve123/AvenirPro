<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResultResource extends JsonResource
{
    /** RIASEC code → human-readable name map */
    private const RIASEC_NAMES = [
        'R' => ['name' => 'Réaliste',       'description' => 'Pratique, physique, aime résoudre des problèmes manuels.'],
        'I' => ['name' => 'Investigateur',  'description' => 'Esprit analytique qui aime la recherche et l\'investigation.'],
        'A' => ['name' => 'Artistique',     'description' => 'Individu créatif et expressif.'],
        'S' => ['name' => 'Social',         'description' => 'Tourné vers les autres, aidant et communicant.'],
        'E' => ['name' => 'Entreprenant',   'description' => 'Leader ambitieux et persuasif.'],
        'C' => ['name' => 'Conventionnel',  'description' => 'Minutieux, organisé et structuré dans son travail.'],
    ];

    public function toArray(Request $request): array
    {
        $rawScores  = $this->riasec_scores ?? [];
        $topProfile = $this->top_profile ?? '';

        // Build top_profiles array: each code in profile string with name + score
        $topProfiles = [];
        foreach (str_split($topProfile) as $code) {
            if (isset(self::RIASEC_NAMES[$code])) {
                $topProfiles[] = [
                    'code'        => $code,
                    'name'        => self::RIASEC_NAMES[$code]['name'],
                    'description' => self::RIASEC_NAMES[$code]['description'],
                    'score'       => $rawScores[$code] ?? 0,
                ];
            }
        }

        // Scores with names for chart
        $scores = [];
        foreach ($rawScores as $code => $score) {
            $scores[$code] = [
                'score' => $score,
                'name'  => self::RIASEC_NAMES[$code]['name'] ?? $code,
            ];
        }

        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'profile'          => $topProfile,
            'top_profiles'     => $topProfiles,
            'scores'           => $scores,
            'riasec_scores'    => $rawScores,
            'recommended_jobs' => $this->recommended_jobs ?? [],
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
