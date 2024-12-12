<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Database extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'type',
        'port',
        'version',
        'charset',
        'collation',
        'notes',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }
} 