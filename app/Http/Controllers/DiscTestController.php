<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscTest;
use App\Models\DiscQuestion;
use App\Models\DiscAnswer;

class DiscTestController extends Controller
{
    public function start()
    {
        return view('disc.start');
    }

    public function storeMeta(Request $request)
    {
        $test = DiscTest::create([
            'nama' => $request->nama,
            'usia' => $request->usia,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_tes' => now(),
        ]);

        return redirect("/test/{$test->id}/question/1");
    }

    public function question(DiscTest $test, $number)
    {
        $question = DiscQuestion::where('question_number', $number)
            ->with('statements')
            ->firstOrFail();

        return view('disc.question', compact('test', 'question', 'number'));
    }

    public function answer(Request $request, DiscTest $test)
    {
        DiscAnswer::updateOrCreate(
            [
                'disc_test_id' => $test->id,
                'disc_question_id' => $request->disc_question_id,
            ],
            [
                'p_statement_id' => $request->p,
                'k_statement_id' => $request->k,
            ]
        );

        $next = $request->question_number + 1;

        return $next <= 24
            ? redirect("/test/{$test->id}/question/{$next}")
            : redirect("/test/{$test->id}/result");
    }
}

