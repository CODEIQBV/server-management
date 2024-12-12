<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Network extends Model
{
    protected $fillable = [
        'name',
        'subnet',
        'notes',
    ];

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
} 