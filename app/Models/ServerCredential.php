<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerCredential extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'username',
        'encrypted_password',
        'type',
        'notes',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
} 