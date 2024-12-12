<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    protected $fillable = [
        'name',
        'hostname',
        'public_ip',
        'internal_ip',
        'ssh_port',
        'datacenter_id',
        'network_id',
        'parent_server_id',
        'auth_type',
        'encrypted_password',
        'ssh_key',
        'notes',
        'status',
    ];

    public function datacenter(): BelongsTo
    {
        return $this->belongsTo(Datacenter::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }

    public function parentServer(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'parent_server_id');
    }

    public function childServers(): HasMany
    {
        return $this->hasMany(Server::class, 'parent_server_id');
    }

    public function credentials()
    {
        return $this->hasMany(ServerCredential::class);
    }

    public function databases()
    {
        return $this->hasMany(Database::class);
    }

    protected $casts = [
        'additional_disks' => 'array',
    ];
} 