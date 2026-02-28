<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Position;
use App\Models\PositionDiscProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index()
    {
        $relations = ['client', 'profile'];
        $hasClientPosition = Schema::hasTable('client_position');
        if ($hasClientPosition) {
            $relations[] = 'clients';
        }

        $positions = Position::with($relations)->latest()->paginate(20);
        $clients = Client::orderBy('name')->get();

        return view('admin.positions.index', compact('positions', 'clients', 'hasClientPosition'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'test_type' => ['required', 'string', 'max:50'],
            'is_global' => ['nullable', 'boolean'],
            'd_target' => ['required', 'integer', 'min:0', 'max:100'],
            'i_target' => ['required', 'integer', 'min:0', 'max:100'],
            's_target' => ['required', 'integer', 'min:0', 'max:100'],
            'c_target' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $clientId = $validated['client_id'] ?? null;

        if (!$clientId && !empty($validated['client_name'])) {
            $client = Client::firstOrCreate(
                ['name' => $validated['client_name']],
                ['code' => Str::slug($validated['client_name']) . '-' . Str::lower(Str::random(5))]
            );
            $clientId = $client->id;
        }

        $isGlobal = (bool) ($validated['is_global'] ?? false);

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

        PositionDiscProfile::create([
            'position_id' => $position->id,
            'test_type' => strtoupper($validated['test_type']),
            'd_target' => $validated['d_target'],
            'i_target' => $validated['i_target'],
            's_target' => $validated['s_target'],
            'c_target' => $validated['c_target'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);

        if ($clientId && Schema::hasTable('client_position')) {
            $position->clients()->syncWithoutDetaching([$clientId]);
        }

        return back()->with('success', 'Posisi dan kombinasi profil tes berhasil ditambahkan.');
    }

    public function toggle(Position $position)
    {
        $position->update(['is_active' => !$position->is_active]);

        if ($position->profile) {
            $position->profile->update(['is_active' => $position->is_active]);
        }

        return back()->with('success', 'Status posisi diperbarui.');
    }

    public function updateProfile(Request $request, Position $position)
    {
        $validated = $request->validate([
            'd_target' => ['required', 'integer', 'min:0', 'max:100'],
            'i_target' => ['required', 'integer', 'min:0', 'max:100'],
            's_target' => ['required', 'integer', 'min:0', 'max:100'],
            'c_target' => ['required', 'integer', 'min:0', 'max:100'],
            'test_type' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        PositionDiscProfile::updateOrCreate(
            ['position_id' => $position->id],
            [
                ...$validated,
                'test_type' => strtoupper($validated['test_type'] ?? optional($position->profile)->test_type ?? 'DISC'),
                'is_active' => $position->is_active,
            ]
        );

        return back()->with('success', 'Kombinasi profil posisi berhasil diperbarui.');
    }

    public function attachClient(Request $request, Position $position)
    {
        if (!Schema::hasTable('client_position')) {
            return back()->withErrors([
                'client_id' => 'Tabel client_position belum ada. Jalankan migrasi terbaru terlebih dahulu.',
            ]);
        }

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
        ]);

        $position->clients()->syncWithoutDetaching([$validated['client_id']]);

        return back()->with('success', 'Client berhasil ditambahkan ke posisi.');
    }

    public function detachClient(Position $position, Client $client)
    {
        if (!Schema::hasTable('client_position')) {
            return back()->withErrors([
                'client_id' => 'Tabel client_position belum ada. Jalankan migrasi terbaru terlebih dahulu.',
            ]);
        }

        $position->clients()->detach($client->id);

        return back()->with('success', 'Client berhasil dilepas dari posisi.');
    }
}
