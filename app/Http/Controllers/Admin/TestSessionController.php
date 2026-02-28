<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestSessionController extends Controller
{
    public function index()
    {
        $sessions = TestSession::with('client')->latest()->paginate(20);
        $clients = Client::orderBy('name')->get();

        return view('admin.sessions.index', compact('sessions', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:test_sessions,code'],
            'test_type' => ['required', 'string', 'max:50'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $clientId = $validated['client_id'] ?? null;

        if (!$clientId && !empty($validated['client_name'])) {
            $client = Client::firstOrCreate(
                ['name' => $validated['client_name']],
                ['code' => Str::slug($validated['client_name']) . '-' . Str::lower(Str::random(5))]
            );
            $clientId = $client->id;
        }

        TestSession::create([
            'name' => $validated['name'],
            'code' => Str::upper($validated['code']),
            'test_type' => Str::upper($validated['test_type']),
            'client_id' => $clientId,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kode sesi berhasil dibuat.');
    }

    public function toggle(TestSession $session)
    {
        $session->update(['is_active' => !$session->is_active]);

        return back()->with('success', 'Status sesi diperbarui.');
    }
}
