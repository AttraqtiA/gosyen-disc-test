<?php

namespace App\Services;

use App\Models\DiscResult;
use App\Models\DiscTest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscNarrativeService
{
    public function generate(DiscTest $test, DiscResult $result, Collection $recommendations): ?string
    {
        $enabled = (bool) env('DISC_ENABLE_GPT_NARRATIVE', false);
        $apiKey = config('services.openai.api_key');

        if (!$enabled || !$apiKey) {
            return null;
        }

        $model = config('services.openai.model', 'gpt-4.1-mini');

        $top = $recommendations->take(3)->map(function ($rec) {
            return sprintf('%s (skor kecocokan %.2f)', $rec->position->title, $rec->match_score);
        })->implode('; ');

        $prompt = "Nama: {$test->nama}\n"
            . "Institusi: {$test->institusi_perusahaan}\n"
            . "Departemen: {$test->departemen_divisi}\n"
            . "Skor DISC: D={$result->d_score}, I={$result->i_score}, S={$result->s_score}, C={$result->c_score}\n"
            . "Dominan: {$result->dominant_type}\n"
            . "Top posisi: {$top}\n\n"
            . "Buat narasi singkat (maksimal 120 kata) dalam bahasa Indonesia profesional. "
            . "Format: ringkasan karakter kerja + saran pengembangan + catatan bahwa hasil DISC bersifat indikatif, bukan diagnosis.";

        try {
            $response = Http::timeout(20)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => $model,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => [
                                ['type' => 'input_text', 'text' => 'Anda adalah psikometri assistant untuk HR. Jawaban ringkas, netral, dan non-diagnostik.'],
                            ],
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'input_text', 'text' => $prompt],
                            ],
                        ],
                    ],
                    'max_output_tokens' => 220,
                    'temperature' => 0.4,
                ]);

            if (!$response->successful()) {
                Log::warning('OpenAI narrative request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $json = $response->json();
            $text = data_get($json, 'output.0.content.0.text')
                ?? data_get($json, 'output_text');

            return is_string($text) ? trim($text) : null;
        } catch (\Throwable $e) {
            Log::warning('OpenAI narrative error', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
