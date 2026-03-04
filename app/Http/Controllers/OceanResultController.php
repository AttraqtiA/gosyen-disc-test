<?php

namespace App\Http\Controllers;

use App\Models\OceanRecommendation;
use App\Models\OceanResult;
use App\Models\OceanTest;
use App\Models\Position;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class OceanResultController extends Controller
{
    public function show(OceanTest $test)
    {
        $hasClientPosition = Schema::hasTable('client_position');
        $test->load('answers.question');
        $answered = $test->answers()->count();
        $totalQuestions = \App\Models\OceanQuestion::count();
        $result = $this->calculateAndStoreScores($test);
        $recommendations = $this->generateAndStoreRecommendations($test, $result);

        return view('ocean.result', compact(
            'test',
            'answered',
            'totalQuestions',
            'result',
            'recommendations',
            'hasClientPosition'
        ));
    }

    private function calculateAndStoreScores(OceanTest $test): OceanResult
    {
        if (!Schema::hasTable('ocean_results')) {
            return new OceanResult([
                'ocean_test_id' => $test->id,
                'o_score' => 0,
                'c_score' => 0,
                'e_score' => 0,
                'a_score' => 0,
                'n_score' => 0,
                'dominant_trait' => null,
            ]);
        }

        $scores = [
            'O' => 0,
            'C' => 0,
            'E' => 0,
            'A' => 0,
            'N' => 0,
        ];

        foreach ($test->answers as $answer) {
            $trait = optional($answer->question)->trait;
            $raw = (int) $answer->score;
            $final = optional($answer->question)->reverse_scored ? (6 - $raw) : $raw;

            if (isset($scores[$trait])) {
                $scores[$trait] += $final;
            }
        }

        $dominant = collect($scores)->sortDesc()->keys()->first();

        return OceanResult::updateOrCreate(
            ['ocean_test_id' => $test->id],
            [
                'o_score' => $scores['O'],
                'c_score' => $scores['C'],
                'e_score' => $scores['E'],
                'a_score' => $scores['A'],
                'n_score' => $scores['N'],
                'dominant_trait' => $dominant,
            ]
        );
    }

    private function generateAndStoreRecommendations(OceanTest $test, OceanResult $result): Collection
    {
        if (
            !Schema::hasTable('positions') ||
            !Schema::hasTable('position_ocean_profiles') ||
            !Schema::hasTable('ocean_recommendations')
        ) {
            return collect();
        }

        $testType = 'OCEAN';
        $relations = ['client', 'oceanProfiles'];
        $hasClientPosition = Schema::hasTable('client_position');
        if ($hasClientPosition) {
            $relations[] = 'clients';
        }

        $positionQuery = Position::query()
            ->with($relations)
            ->where('is_active', true)
            ->whereHas('oceanProfiles', function ($query) use ($testType) {
                $query->where('is_active', true)->where('test_type', $testType);
            });

        $positions = collect();
        if ($test->client_id) {
            $positions = (clone $positionQuery)->where(function ($query) use ($test) {
                $query->where('is_global', true);

                if (Schema::hasTable('client_position')) {
                    $query->orWhereHas('clients', function ($clientQuery) use ($test) {
                        $clientQuery->where('clients.id', $test->client_id);
                    });
                }

                $query->orWhere('client_id', $test->client_id);
            })->get();
        }

        if ($positions->isEmpty()) {
            $positions = $positionQuery->get();
        }

        OceanRecommendation::where('ocean_test_id', $test->id)->delete();
        if ($positions->isEmpty()) {
            return collect();
        }

        $testVector = $this->normalizeVector([
            $result->o_score,
            $result->c_score,
            $result->e_score,
            $result->a_score,
            $result->n_score,
        ]);

        $ranked = $positions->map(function (Position $position) use ($testVector) {
            $profile = $position->oceanProfiles->firstWhere('test_type', 'OCEAN');
            if (!$profile) {
                return null;
            }

            $distance = 0.0;
            $distance += abs($testVector['O'] - $profile->o_target);
            $distance += abs($testVector['C'] - $profile->c_target);
            $distance += abs($testVector['E'] - $profile->e_target);
            $distance += abs($testVector['A'] - $profile->a_target);
            $distance += abs($testVector['N'] - $profile->n_target);

            $score = max(0, 100 - ($distance / 5));

            return [
                'position' => $position,
                'match_score' => round($score, 2),
            ];
        })->filter()->sortByDesc('match_score')->values();

        $ranked->each(function (array $item, int $index) use ($test) {
            OceanRecommendation::create([
                'ocean_test_id' => $test->id,
                'position_id' => $item['position']->id,
                'match_score' => $item['match_score'],
                'rank' => $index + 1,
                'test_type' => 'OCEAN',
                'source' => 'deterministic',
            ]);
        });

        return OceanRecommendation::where('ocean_test_id', $test->id)
            ->with($hasClientPosition ? ['position.client', 'position.clients'] : ['position.client'])
            ->orderBy('rank')
            ->get();
    }

    private function normalizeVector(array $values): array
    {
        $sum = array_sum($values);
        if ($sum <= 0) {
            return ['O' => 20.0, 'C' => 20.0, 'E' => 20.0, 'A' => 20.0, 'N' => 20.0];
        }

        return [
            'O' => round(($values[0] / $sum) * 100, 2),
            'C' => round(($values[1] / $sum) * 100, 2),
            'E' => round(($values[2] / $sum) * 100, 2),
            'A' => round(($values[3] / $sum) * 100, 2),
            'N' => round(($values[4] / $sum) * 100, 2),
        ];
    }
}
