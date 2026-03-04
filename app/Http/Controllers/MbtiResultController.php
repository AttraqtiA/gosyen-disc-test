<?php

namespace App\Http\Controllers;

use App\Models\MbtiRecommendation;
use App\Models\MbtiResult;
use App\Models\MbtiTest;
use App\Models\Position;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MbtiResultController extends Controller
{
    public function show(MbtiTest $test)
    {
        $hasClientPosition = Schema::hasTable('client_position');
        $test->load('answers.question');
        $answered = $test->answers()->count();
        $totalQuestions = \App\Models\MbtiQuestion::count();
        $result = $this->calculateAndStoreScores($test);
        $recommendations = $this->generateAndStoreRecommendations($test, $result);

        return view('mbti.result', compact(
            'test',
            'answered',
            'totalQuestions',
            'result',
            'recommendations',
            'hasClientPosition'
        ));
    }

    private function calculateAndStoreScores(MbtiTest $test): MbtiResult
    {
        if (!Schema::hasTable('mbti_results')) {
            return new MbtiResult([
                'mbti_test_id' => $test->id,
                'e_score' => 0,
                'i_score' => 0,
                's_score' => 0,
                'n_score' => 0,
                't_score' => 0,
                'f_score' => 0,
                'j_score' => 0,
                'p_score' => 0,
                'type_code' => null,
            ]);
        }

        $scores = [
            'E' => 0,
            'I' => 0,
            'S' => 0,
            'N' => 0,
            'T' => 0,
            'F' => 0,
            'J' => 0,
            'P' => 0,
        ];

        foreach ($test->answers as $answer) {
            $trait = $answer->selected_trait;
            if (isset($scores[$trait])) {
                $scores[$trait] += 1;
            }
        }

        $typeCode = $this->composeTypeCode($scores);

        return MbtiResult::updateOrCreate(
            ['mbti_test_id' => $test->id],
            [
                'e_score' => $scores['E'],
                'i_score' => $scores['I'],
                's_score' => $scores['S'],
                'n_score' => $scores['N'],
                't_score' => $scores['T'],
                'f_score' => $scores['F'],
                'j_score' => $scores['J'],
                'p_score' => $scores['P'],
                'type_code' => $typeCode,
            ]
        );
    }

    private function composeTypeCode(array $scores): string
    {
        return ($scores['E'] >= $scores['I'] ? 'E' : 'I')
            . ($scores['S'] >= $scores['N'] ? 'S' : 'N')
            . ($scores['T'] >= $scores['F'] ? 'T' : 'F')
            . ($scores['J'] >= $scores['P'] ? 'J' : 'P');
    }

    private function generateAndStoreRecommendations(MbtiTest $test, MbtiResult $result): Collection
    {
        if (
            !Schema::hasTable('positions') ||
            !Schema::hasTable('position_mbti_profiles') ||
            !Schema::hasTable('mbti_recommendations')
        ) {
            return collect();
        }

        $testType = 'MBTI';
        $relations = ['client', 'mbtiProfiles'];
        $hasClientPosition = Schema::hasTable('client_position');
        if ($hasClientPosition) {
            $relations[] = 'clients';
        }

        $positionQuery = Position::query()
            ->with($relations)
            ->where('is_active', true)
            ->whereHas('mbtiProfiles', function ($query) use ($testType) {
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

        MbtiRecommendation::where('mbti_test_id', $test->id)->delete();
        if ($positions->isEmpty()) {
            return collect();
        }

        $testVector = $this->toPairPercentages($result);

        $ranked = $positions->map(function (Position $position) use ($testVector, $testType) {
            $profile = $position->mbtiProfiles->firstWhere('test_type', $testType);
            if (!$profile) {
                return null;
            }

            $distance = 0.0;
            $distance += abs($testVector['E'] - $profile->e_target);
            $distance += abs($testVector['I'] - $profile->i_target);
            $distance += abs($testVector['S'] - $profile->s_target);
            $distance += abs($testVector['N'] - $profile->n_target);
            $distance += abs($testVector['T'] - $profile->t_target);
            $distance += abs($testVector['F'] - $profile->f_target);
            $distance += abs($testVector['J'] - $profile->j_target);
            $distance += abs($testVector['P'] - $profile->p_target);

            $score = max(0, 100 - ($distance / 8));

            return [
                'position' => $position,
                'match_score' => round($score, 2),
            ];
        })->filter()->sortByDesc('match_score')->values();

        $ranked->each(function (array $item, int $index) use ($test, $testType) {
            MbtiRecommendation::create([
                'mbti_test_id' => $test->id,
                'position_id' => $item['position']->id,
                'match_score' => $item['match_score'],
                'rank' => $index + 1,
                'test_type' => $testType,
                'source' => 'deterministic',
            ]);
        });

        return MbtiRecommendation::where('mbti_test_id', $test->id)
            ->with($hasClientPosition ? ['position.client', 'position.clients'] : ['position.client'])
            ->orderBy('rank')
            ->get();
    }

    private function toPairPercentages(MbtiResult $result): array
    {
        return [
            'E' => $this->pairPercentage($result->e_score, $result->i_score),
            'I' => $this->pairPercentage($result->i_score, $result->e_score),
            'S' => $this->pairPercentage($result->s_score, $result->n_score),
            'N' => $this->pairPercentage($result->n_score, $result->s_score),
            'T' => $this->pairPercentage($result->t_score, $result->f_score),
            'F' => $this->pairPercentage($result->f_score, $result->t_score),
            'J' => $this->pairPercentage($result->j_score, $result->p_score),
            'P' => $this->pairPercentage($result->p_score, $result->j_score),
        ];
    }

    private function pairPercentage(int $left, int $right): float
    {
        $sum = $left + $right;
        if ($sum <= 0) {
            return 50.0;
        }

        return round(($left / $sum) * 100, 2);
    }
}
