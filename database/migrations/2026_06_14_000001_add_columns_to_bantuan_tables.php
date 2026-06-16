<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Samakan dengan kolom kontak_donatur agar form & tampilan detail konsisten
        Schema::table('permintaan_bantuan', function (Blueprint $table) {
            $table->string('kontak_pj', 20)->nullable()->after('jabatan');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });

        Schema::table('penawaran_bantuan', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_bantuan', function (Blueprint $table) {
            $table->dropColumn(['kontak_pj', 'updated_at']);
        });

        Schema::table('penawaran_bantuan', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
