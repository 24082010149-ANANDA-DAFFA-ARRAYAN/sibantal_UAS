<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'desa', 'donatur']);
            $table->string('asal_desa', 100)->nullable();
            $table->string('nama_organisasi', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
