<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Position;
use App\Models\PositionDiscProfile;
use App\Models\PositionMbtiProfile;
use App\Models\PositionOceanProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $relations = ['client', 'profile', 'mbtiProfiles', 'oceanProfiles'];
        $hasClientPosition = Schema::hasTable('client_position');
        if ($hasClientPosition) {
            $relations[] = 'clients';
        }

        $positions = Position::with($relations)
            ->when($user->isClientAdmin(), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('client_id', $user->client_id);

                    if (Schema::hasTable('client_position')) {
                        $q->orWhereHas('clients', fn ($cq) => $cq->where('clients.id', $user->client_id));
                    }
                });
            })
            ->latest()
            ->paginate(20);

        $clients = $user->isClientAdmin()
            ? Client::whereKey($user->client_id)->get()
            : Client::orderBy('name')->get();

        return view('admin.positions.index', compact('positions', 'clients', 'hasClientPosition'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'test_type' => ['required', 'string', 'max:50'],
            'is_global' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $testType = strtoupper($validated['test_type']);
        $profileData = $this->validateProfileInput($request, $testType);

        $clientId = $validated['client_id'] ?? null;

        if ($user->isClientAdmin()) {
            $clientId = $user->client_id;
        } elseif (!$clientId && !empty($validated['client_name'])) {
            $client = Client::firstOrCreate(
                ['name' => $validated['client_name']],
                ['code' => Str::slug($validated['client_name']) . '-' . Str::lower(Str::random(5))]
            );
            $clientId = $client->id;
        }

        $isGlobal = $user->isClientAdmin() ? false : (bool) ($validated['is_global'] ?? false);

        if (!$clientId && !$isGlobal) {
            return back()->withErrors([
                'client_id' => 'Pilih client, isi client baru, atau aktifkan posisi global.',
            ])->withInput();
        }

        $position = Position::create([
            'client_id' => $clientId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'is_global' => $isGlobal,
        ]);

        $this->createOrUpdateProfile($position, $testType, [
            ...$profileData,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($clientId && Schema::hasTable('client_position')) {
            $position->clients()->syncWithoutDetaching([$clientId]);
        }

        return back()->with('success', 'Posisi dan kombinasi profil tes berhasil ditambahkan.');
    }

    public function toggle(Request $request, Position $position)
    {
        $this->authorizePosition($request, $position);

        $position->update(['is_active' => !$position->is_active]);

        if ($position->profile) {
            $position->profile->update(['is_active' => $position->is_active]);
        }
        $position->mbtiProfiles()->update(['is_active' => $position->is_active]);
        $position->oceanProfiles()->update(['is_active' => $position->is_active]);

        return back()->with('success', 'Status posisi diperbarui.');
    }

    public function updateProfile(Request $request, Position $position)
    {
        $this->authorizePosition($request, $position);

        $validated = $request->validate([
            'test_type' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $testType = strtoupper($validated['test_type']);
        $profileData = $this->validateProfileInput($request, $testType);

        $this->createOrUpdateProfile($position, $testType, [
            ...$profileData,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Kombinasi profil posisi berhasil diperbarui.');
    }

    public function attachClient(Request $request, Position $position)
    {
        $this->authorizePosition($request, $position);

        if (!Schema::hasTable('client_position')) {
            return back()->withErrors([
                'client_id' => 'Tabel client_position belum ada. Jalankan migrasi terbaru terlebih dahulu.',
            ]);
        }

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
        ]);

        $user = $request->user();
        if ($user->isClientAdmin() && (int) $validated['client_id'] !== (int) $user->client_id) {
            abort(403, 'Client admin hanya bisa menautkan client miliknya.');
        }

        $position->clients()->syncWithoutDetaching([$validated['client_id']]);

        return back()->with('success', 'Client berhasil ditambahkan ke posisi.');
    }

    public function detachClient(Request $request, Position $position, Client $client)
    {
        $this->authorizePosition($request, $position);

        if (!Schema::hasTable('client_position')) {
            return back()->withErrors([
                'client_id' => 'Tabel client_position belum ada. Jalankan migrasi terbaru terlebih dahulu.',
            ]);
        }

        $user = $request->user();
        if ($user->isClientAdmin() && (int) $client->id !== (int) $user->client_id) {
            abort(403, 'Client admin hanya bisa melepas client miliknya.');
        }

        $position->clients()->detach($client->id);

        return back()->with('success', 'Client berhasil dilepas dari posisi.');
    }

    private function authorizePosition(Request $request, Position $position): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if ($user->isClientAdmin()) {
            if ((int) $position->client_id === (int) $user->client_id) {
                return;
            }

            if (Schema::hasTable('client_position')) {
                $attached = $position->clients()->where('clients.id', $user->client_id)->exists();
                if ($attached) {
                    return;
                }
            }
        }

        abort(403, 'Anda tidak memiliki akses ke posisi ini.');
    }

    private function validateProfileInput(Request $request, string $testType): array
    {
        if ($testType === 'DISC') {
            return $request->validate([
                'd_target' => ['required', 'integer', 'min:0', 'max:100'],
                'i_target' => ['required', 'integer', 'min:0', 'max:100'],
                's_target' => ['required', 'integer', 'min:0', 'max:100'],
                'c_target' => ['required', 'integer', 'min:0', 'max:100'],
            ]);
        }

        if ($testType === 'MBTI') {
            return $request->validate([
                'e_target' => ['required', 'integer', 'min:0', 'max:100'],
                'i_target' => ['required', 'integer', 'min:0', 'max:100'],
                's_target' => ['required', 'integer', 'min:0', 'max:100'],
                'n_target' => ['required', 'integer', 'min:0', 'max:100'],
                't_target' => ['required', 'integer', 'min:0', 'max:100'],
                'f_target' => ['required', 'integer', 'min:0', 'max:100'],
                'j_target' => ['required', 'integer', 'min:0', 'max:100'],
                'p_target' => ['required', 'integer', 'min:0', 'max:100'],
            ]);
        }

        if ($testType === 'OCEAN') {
            return $request->validate([
                'o_target' => ['required', 'integer', 'min:0', 'max:100'],
                'c_target' => ['required', 'integer', 'min:0', 'max:100'],
                'e_target' => ['required', 'integer', 'min:0', 'max:100'],
                'a_target' => ['required', 'integer', 'min:0', 'max:100'],
                'n_target' => ['required', 'integer', 'min:0', 'max:100'],
            ]);
        }

        abort(422, 'Tipe tes tidak didukung untuk profil posisi.');
    }

    private function createOrUpdateProfile(Position $position, string $testType, array $data): void
    {
        if ($testType === 'DISC') {
            PositionDiscProfile::updateOrCreate(
                ['position_id' => $position->id],
                [
                    'test_type' => 'DISC',
                    'd_target' => $data['d_target'],
                    'i_target' => $data['i_target'],
                    's_target' => $data['s_target'],
                    'c_target' => $data['c_target'],
                    'notes' => $data['notes'] ?? null,
                    'is_active' => $position->is_active,
                ]
            );
            return;
        }

        if ($testType === 'OCEAN') {
            PositionOceanProfile::updateOrCreate(
                ['position_id' => $position->id, 'test_type' => 'OCEAN'],
                [
                    'o_target' => $data['o_target'],
                    'c_target' => $data['c_target'],
                    'e_target' => $data['e_target'],
                    'a_target' => $data['a_target'],
                    'n_target' => $data['n_target'],
                    'notes' => $data['notes'] ?? null,
                    'is_active' => $position->is_active,
                ]
            );
            return;
        }

        PositionMbtiProfile::updateOrCreate(
            ['position_id' => $position->id, 'test_type' => $testType],
            [
                'e_target' => $data['e_target'],
                'i_target' => $data['i_target'],
                's_target' => $data['s_target'],
                'n_target' => $data['n_target'],
                't_target' => $data['t_target'],
                'f_target' => $data['f_target'],
                'j_target' => $data['j_target'],
                'p_target' => $data['p_target'],
                'notes' => $data['notes'] ?? null,
                'is_active' => $position->is_active,
            ]
        );
    }
}
