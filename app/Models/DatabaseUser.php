<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatabaseUser extends Model
{
    protected $fillable = [
        'database_id',
        'username',
        'encrypted_password',
        'privileges',
        'notes',
    ];

    protected $casts = [
        'privileges' => 'array',
    ];

    public function database(): BelongsTo
    {
        return $this->belongsTo(Database::class);
    }
} 