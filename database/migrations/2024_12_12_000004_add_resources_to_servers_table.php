<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // Server Resources
            $table->integer('cpu_cores')->nullable();
            $table->integer('cpu_threads')->nullable();
            $table->integer('ram_gb')->nullable();
            $table->integer('disk_gb')->nullable();
            $table->string('disk_type')->nullable(); // SSD, NVMe, etc.
            $table->json('additional_disks')->nullable(); // For multiple disks
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn([
                'cpu_cores',
                'cpu_threads',
                'ram_gb',
                'disk_gb',
                'disk_type',
                'additional_disks',
            ]);
        });
    }
}; 