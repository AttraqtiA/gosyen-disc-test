<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTest;
use App\Models\CustomTestDimension;
use App\Models\CustomTestOption;
use App\Models\CustomTestQuestion;
use App\Models\Position;
use App\Models\PositionCustomTestProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomTestController extends Controller
{
    public function index()
    {
        $tests = CustomTest::query()
            ->withCount(['dimensions', 'questions'])
            ->latest()
            ->paginate(15);

        return view('admin.custom-tests.index', compact('tests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:custom_tests,code'],
            'description' => ['nullable', 'string', 'max:2000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
            'instructions' => ['nullable', 'string', 'max:5000'],
        ]);

        CustomTest::create([
            'name' => $validated['name'],
            'code' => Str::upper($validated['code']),
            'description' => $validated['description'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Custom test berhasil dibuat.');
    }

    public function toggle(CustomTest $test)
    {
        $test->update(['is_active' => !$test->is_active]);

        return back()->with('success', 'Status custom test diperbarui.');
    }

    public function update(Request $request, CustomTest $test)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('custom_tests', 'code')->ignore($test->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
            'instructions' => ['nullable', 'string', 'max:5000'],
        ]);

        $test->update([
            'name' => $validated['name'],
            'code' => Str::upper($validated['code']),
            'description' => $validated['description'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
        ]);

        return back()->with('success', 'Custom test berhasil diperbarui.');
    }

    public function destroy(CustomTest $test)
    {
        $test->delete();

        return back()->with('success', 'Custom test berhasil dihapus.');
    }

    public function show(CustomTest $test)
    {
        $test->load([
            'dimensions',
            'questions.options',
            'positionProfiles.position.client',
            'positionProfiles.position.clients',
        ]);

        $positions = Position::query()
            ->with(['client', 'clients'])
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.custom-tests.show', compact('test', 'positions'));
    }

    public function storeDimension(Request $request, CustomTest $test)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9_]+$/'],
            'name' => ['required', 'string', 'max:100'],
            'weight' => ['nullable', 'integer', 'min:1', 'max:10'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        CustomTestDimension::updateOrCreate(
            [
                'custom_test_id' => $test->id,
                'code' => Str::upper($validated['code']),
            ],
            [
                'name' => $validated['name'],
                'weight' => $validated['weight'] ?? 1,
                'sort_order' => $validated['sort_order'] ?? ($test->dimensions()->max('sort_order') + 1),
            ]
        );

        return back()->with('success', 'Dimensi berhasil disimpan.');
    }

    public function storeQuestion(Request $request, CustomTest $test)
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        CustomTestQuestion::create([
            'custom_test_id' => $test->id,
            'question_text' => $validated['question_text'],
            'question_type' => 'single_choice',
            'sort_order' => $validated['sort_order'] ?? ($test->questions()->max('sort_order') + 1),
            'is_required' => (bool) ($validated['is_required'] ?? true),
        ]);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function storeOption(Request $request, CustomTest $test, CustomTestQuestion $question)
    {
        if ($question->custom_test_id !== $test->id) {
            abort(404);
        }
        if ($test->dimensions->isEmpty()) {
            return back()->withErrors([
                'option_text' => 'Tambahkan minimal 1 dimensi sebelum membuat opsi jawaban.',
            ]);
        }

        $validated = $request->validate([
            'option_text' => ['required', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ]);

        $scores = [];
        foreach ($test->dimensions as $dimension) {
            $value = $request->input('score_' . Str::lower($dimension->code), 0);
            $scores[$dimension->code] = (int) $value;
        }

        CustomTestOption::create([
            'custom_test_question_id' => $question->id,
            'option_text' => $validated['option_text'],
            'scores_json' => $scores,
            'sort_order' => $validated['sort_order'] ?? ($question->options()->max('sort_order') + 1),
        ]);

        return back()->with('success', 'Opsi jawaban + logic skor berhasil ditambahkan.');
    }

    public function upsertPositionRule(Request $request, CustomTest $test)
    {
        if ($test->dimensions->isEmpty()) {
            return back()->withErrors([
                'position_id' => 'Tambahkan minimal 1 dimensi sebelum membuat rule posisi.',
            ]);
        }

        $validated = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $scores = [];
        foreach ($test->dimensions as $dimension) {
            $key = 'target_' . Str::lower($dimension->code);
            $scores[$dimension->code] = (int) $request->input($key, 0);
        }

        PositionCustomTestProfile::updateOrCreate(
            [
                'position_id' => $validated['position_id'],
                'custom_test_id' => $test->id,
            ],
            [
                'target_scores_json' => $scores,
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Rule rekomendasi posisi berhasil disimpan.');
    }
}
