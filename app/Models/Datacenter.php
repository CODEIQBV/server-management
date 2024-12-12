<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Datacenter extends Model
{
    protected $fillable = [
        'name',
        'location',
        'notes',
    ];

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
} 