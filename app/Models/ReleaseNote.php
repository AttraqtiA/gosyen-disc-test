<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleaseNote extends Model
{
    protected $fillable = [
        'version',
        'git_commit',
        'operator',
        'environment',
        'status',
        'migrated',
        'notes',
        'released_at',
    ];

    protected $casts = [
        'migrated' => 'boolean',
        'released_at' => 'datetime',
    ];
}

