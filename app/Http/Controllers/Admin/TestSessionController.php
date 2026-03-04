<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TestSessionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $sessions = TestSession::with('client')
            ->when($user->isClientAdmin(), function ($query) use ($user) {
                $query->where('client_id', $user->client_id);
            })
            ->latest()
            ->paginate(20);

        $clients = $user->isClientAdmin()
            ? Client::whereKey($user->client_id)->get()
            : Client::orderBy('name')->get();

        return view('admin.sessions.index', compact('sessions', 'clients'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:test_sessions,code'],
            'test_type' => ['required', 'string', 'max:50'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
        ]);

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

    public function toggle(Request $request, TestSession $session)
    {
        $this->authorizeSession($request, $session);

        $session->update(['is_active' => !$session->is_active]);

        return back()->with('success', 'Status sesi diperbarui.');
    }

    public function update(Request $request, TestSession $session)
    {
        $this->authorizeSession($request, $session);

        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('test_sessions', 'code')->ignore($session->id),
            ],
            'test_type' => ['required', 'string', 'max:50'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
        ]);

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

        $session->update([
            'name' => $validated['name'],
            'code' => Str::upper($validated['code']),
            'test_type' => Str::upper($validated['test_type']),
            'client_id' => $clientId,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return back()->with('success', 'Sesi berhasil diperbarui.');
    }

    public function destroy(Request $request, TestSession $session)
    {
        $this->authorizeSession($request, $session);

        $session->delete();

        return back()->with('success', 'Sesi berhasil dihapus.');
    }

    private function authorizeSession(Request $request, TestSession $session): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if ($user->isClientAdmin() && $session->client_id === $user->client_id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke sesi ini.');
    }
}
