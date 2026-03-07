<?php

use App\Models\ReleaseNote;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'release:note {version : Versi release, contoh v1.4.2}
    {--status=success : success|failed|started}
    {--migrated=1 : 1 jika migration dijalankan, 0 jika tidak}
    {--commit= : Git commit hash}
    {--operator= : Nama operator deploy}
    {--target-env=production : Environment deploy}
    {--notes= : Catatan release}',
    function () {
        $status = strtolower((string) $this->option('status'));
        if (!in_array($status, ['success', 'failed', 'started'], true)) {
            $this->error('Status harus salah satu dari: success, failed, started.');
            return self::FAILURE;
        }

        $note = ReleaseNote::create([
            'version' => trim((string) $this->argument('version')),
            'git_commit' => trim((string) $this->option('commit')) ?: null,
            'operator' => trim((string) $this->option('operator')) ?: null,
            'environment' => trim((string) $this->option('target-env')) ?: 'production',
            'status' => $status,
            'migrated' => filter_var($this->option('migrated'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            'notes' => trim((string) $this->option('notes')) ?: null,
            'released_at' => now(),
        ]);

        $this->info('Release note tersimpan.');
        $this->table(
            ['ID', 'Version', 'Status', 'Migrated', 'Environment', 'Operator', 'Commit', 'Released At'],
            [[
                $note->id,
                $note->version,
                $note->status,
                $note->migrated ? 'yes' : 'no',
                $note->environment,
                $note->operator ?? '-',
                $note->git_commit ?? '-',
                $note->released_at->format('Y-m-d H:i:s'),
            ]]
        );

        return self::SUCCESS;
    }
)->purpose('Catat release deployment untuk audit manual production');
