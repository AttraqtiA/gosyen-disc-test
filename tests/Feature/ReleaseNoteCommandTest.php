<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseNoteCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_release_note_command_persists_data(): void
    {
        $this->artisan('release:note', [
            'version' => 'v1.0.0',
            '--status' => 'success',
            '--migrated' => '1',
            '--commit' => 'abc123def456',
            '--operator' => 'samuel',
            '--target-env' => 'production',
            '--notes' => 'Deploy awal DISC production',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('release_notes', [
            'version' => 'v1.0.0',
            'status' => 'success',
            'migrated' => 1,
            'git_commit' => 'abc123def456',
            'operator' => 'samuel',
            'environment' => 'production',
            'notes' => 'Deploy awal DISC production',
        ]);
    }

    public function test_release_note_command_rejects_invalid_status(): void
    {
        $this->artisan('release:note', [
            'version' => 'v1.0.1',
            '--status' => 'invalid',
        ])->assertExitCode(1);
    }
}
