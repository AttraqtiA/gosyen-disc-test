<?php

namespace App\Http\Controllers;

use App\Models\DiscRecommendation;
use App\Models\DiscResult;
use App\Models\DiscTest;
use App\Models\Position;
use App\Services\DiscNarrativeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DiscResultController extends Controller
{
    public function show(DiscTest $test, DiscNarrativeService $narrativeService)
    {
        $test->load(['answers.pStatement', 'answers.kStatement']);
        $answered = $test->answers()->count();
        $totalQuestions = 24;
        $result = $this->calculateAndStoreScores($test);
        $recommendations = $this->generateAndStoreRecommendations($test, $result);
        $narrative = $narrativeService->generate($test, $result, $recommendations);

        return view('disc.result', compact(
            'test',
            'answered',
            'totalQuestions',
            'result',
            'recommendations',
            'narrative'
        ));
    }

    private function calculateAndStoreScores(DiscTest $test): DiscResult
    {
        if (!Schema::hasTable('disc_results')) {
            return new DiscResult([
                'disc_test_id' => $test->id,
                'd_score' => 0,
                'i_score' => 0,
                's_score' => 0,
                'c_score' => 0,
                'dominant_type' => null,
            ]);
        }

        $scores = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];

        foreach ($test->answers as $answer) {
            $pType = optional($answer->pStatement)->disc_type;
            $kType = optional($answer->kStatement)->disc_type;

            if (isset($scores[$pType])) {
                $scores[$pType] += 1;
            }

            if (isset($scores[$kType])) {
                $scores[$kType] -= 1;
            }
        }

        $dominantType = collect($scores)->sortDesc()->keys()->first();

        return DiscResult::updateOrCreate(
            ['disc_test_id' => $test->id],
            [
                'd_score' => $scores['D'],
                'i_score' => $scores['I'],
                's_score' => $scores['S'],
                'c_score' => $scores['C'],
                'dominant_type' => $dominantType,
            ]
        );
    }

    private function generateAndStoreRecommendations(DiscTest $test, DiscResult $result): Collection
    {
        if (
            !Schema::hasTable('positions') ||
            !Schema::hasTable('position_disc_profiles') ||
            !Schema::hasTable('disc_recommendations')
        ) {
            return collect();
        }

        $positionQuery = Position::query()
            ->with(['client', 'profile'])
            ->where('is_active', true)
            ->whereHas('profile', fn ($query) => $query->where('is_active', true));

        $positions = collect();

        if ($test->client_id) {
            $positions = (clone $positionQuery)
                ->where('client_id', $test->client_id)
                ->get();
        }

        if ($positions->isEmpty()) {
            $positions = $positionQuery->get();
        }

        DiscRecommendation::where('disc_test_id', $test->id)->delete();

        if ($positions->isEmpty()) {
            return collect();
        }

        $testVector = $this->normalizeVector([
            $result->d_score,
            $result->i_score,
            $result->s_score,
            $result->c_score,
        ]);

        $ranked = $positions->map(function ($position) use ($testVector) {
            $target = $this->normalizeVector([
                $position->profile->d_target,
                $position->profile->i_target,
                $position->profile->s_target,
                $position->profile->c_target,
            ]);

            $distance = 0.0;
            foreach (['D', 'I', 'S', 'C'] as $key) {
                $distance += abs($testVector[$key] - $target[$key]);
            }

            $score = max(0, 100 - ($distance / 2));

            return [
                'position' => $position,
                'match_score' => round($score, 2),
            ];
        })->sortByDesc('match_score')->values();

        $ranked->each(function ($item, $index) use ($test) {
            DiscRecommendation::create([
                'disc_test_id' => $test->id,
                'position_id' => $item['position']->id,
                'match_score' => $item['match_score'],
                'rank' => $index + 1,
                'source' => 'deterministic',
            ]);
        });

        return DiscRecommendation::where('disc_test_id', $test->id)
            ->with(['position.client'])
            ->orderBy('rank')
            ->get();
    }

    private function normalizeVector(array $values): array
    {
        $min = min($values);
        $shifted = array_map(fn ($value) => $value - $min, $values);
        $sum = array_sum($shifted);

        if ($sum <= 0) {
            return ['D' => 25.0, 'I' => 25.0, 'S' => 25.0, 'C' => 25.0];
        }

        return [
            'D' => ($shifted[0] / $sum) * 100,
            'I' => ($shifted[1] / $sum) * 100,
            'S' => ($shifted[2] / $sum) * 100,
            'C' => ($shifted[3] / $sum) * 100,
        ];
    }
}
