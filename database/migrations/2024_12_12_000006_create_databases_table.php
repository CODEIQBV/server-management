<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('databases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // mysql, postgresql, mongodb, etc.
            $table->integer('port')->nullable();
            $table->string('version')->nullable();
            $table->string('charset')->nullable();
            $table->string('collation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('database_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('database_id')->constrained()->cascadeOnDelete();
            $table->string('username');
            $table->text('encrypted_password');
            $table->json('privileges')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('database_users');
        Schema::dropIfExists('databases');
    }
}; 