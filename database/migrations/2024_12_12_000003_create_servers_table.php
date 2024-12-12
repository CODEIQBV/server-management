<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hostname');
            $table->string('public_ip')->nullable();
            $table->string('internal_ip')->nullable();
            $table->integer('ssh_port')->default(22);
            $table->foreignId('datacenter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('network_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_server_id')->nullable()->references('id')->on('servers')->nullOnDelete();
            $table->enum('auth_type', ['password', 'ssh_key'])->default('ssh_key');
            $table->text('encrypted_password')->nullable();
            $table->text('ssh_key')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
}; 