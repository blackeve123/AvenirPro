<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
    }

    /**
     * Enrich a list of matched jobs with AI-generated reasons.
     *
     * @param  string $profile   e.g. "IAE"
     * @param  array  $scores    e.g. ["I" => 25, "A" => 20, "E" => 18, ...]
     * @param  array  $jobs      each has: id, title, match_pct
     * @return array             same jobs array, each enriched with "reason" key
     */
    public function enrich(string $profile, array $scores, array $jobs): array
    {
        if (empty($this->apiKey) || empty($jobs)) {
            return $this->withEmptyReasons($jobs);
        }

        try {
            $jobList = collect($jobs)->map(fn($j) => [
                'title' => $j['title'],
                'match' => $j['match_pct'],
            ])->values()->toArray();

            $scoresTop = collect($scores)
                ->sortDesc()
                ->take(3)
                ->toArray();

            $prompt = "Tu es un expert en orientation professionnelle de haut niveau (style cabinet de conseil en carrière).\n\n"
                . "CONTEXTE :\n"
                . "- Profil RIASEC de l'utilisateur dominant : {$profile}\n"
                . "- Intensité des scores : " . json_encode($scoresTop, JSON_UNESCAPED_UNICODE) . "\n\n"
                . "MISSION :\n"
                . "Pour chaque métier ci-dessous, génère une raison UNIQUE et PERCUTANTE (1 seule phrase, max 15 mots) qui explique pourquoi ce métier est le match parfait pour ses scores RIASEC.\n"
                . "Sois inspirant mais professionnel. Évite les répétitions comme 'Ton profil'.\n\n"
                . "MÉTIERS À ANALYSER :\n"
                . json_encode($jobList, JSON_UNESCAPED_UNICODE) . "\n\n"
                . "FORMAT DE RÉPONSE :\n"
                . "Réponds EXCLUSIVEMENT avec un tableau JSON d'objets contenant 'title' et 'reason'.\n"
                . "Exemple : [{\"title\":\"Physicien\",\"reason\":\"Votre curiosité analytique naturelle s'épanouira dans la résolution de théorèmes complexes.\"}]";

            $isOpenRouter = str_starts_with($this->apiKey, 'sk-or-v1-');

            if ($isOpenRouter) {
                // Use OpenRouter with OpenAI-compatible payload
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => 'Avenir Pro',
                ])->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'google/gemini-pro-1.5',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 800,
                ]);

                if (!$response->successful()) {
                    Log::warning('OpenRouter API error', ['status' => $response->status(), 'body' => $response->body()]);
                    return $this->withEmptyReasons($jobs);
                }
                $text = $response->json('choices.0.message.content', '');
            } else {
                // Use native Google Gemini endpoint
                $response = Http::timeout(20)->post(
                    $this->endpoint . '?key=' . $this->apiKey,
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature'     => 0.8,
                            'maxOutputTokens' => 800,
                        ],
                    ]
                );

                if (!$response->successful()) {
                    Log::warning('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                    return $this->withEmptyReasons($jobs);
                }
                $text = $response->json('candidates.0.content.parts.0.text', '');
            }
            
            // Robust cleaning of the AI response to extract JSON
            if (preg_match('/\[.*\]/s', $text, $matches)) {
                $text = $matches[0];
            }

            $enriched = json_decode($text, true);

            if (!is_array($enriched)) {
                Log::warning('Gemini decoding failed', ['text_received' => $text]);
                return $this->withEmptyReasons($jobs);
            }

            // Map reasons by title (case-insensitive)
            $reasonMap = collect($enriched)->mapWithKeys(fn($item) => [
                mb_strtolower($item['title'] ?? '') => $item['reason'] ?? ''
            ])->toArray();

            // Merge back
            return array_map(function ($job) use ($reasonMap) {
                $job['reason'] = $reasonMap[mb_strtolower($job['title'])] ?? 'Ce métier correspond à vos aptitudes dominantes.';
                return $job;
            }, $jobs);


        } catch (\Throwable $e) {
            Log::error('GeminiService exception', ['error' => $e->getMessage()]);
            return $this->withEmptyReasons($jobs);
        }
    }

    private function withEmptyReasons(array $jobs): array
    {
        return array_map(function ($job) {
            $job['reason'] = '';
            return $job;
        }, $jobs);
    }
}
