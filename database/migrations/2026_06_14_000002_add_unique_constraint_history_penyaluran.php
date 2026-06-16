<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_penyaluran', function (Blueprint $table) {
            // Satu program (id + tipe) hanya boleh diklaim/didanai sekali.
            // Mencegah dua user mengambil program yang sama secara bersamaan.
            $table->unique(['program_id', 'tipe_program'], 'history_program_tipe_unique');
        });
    }

    public function down(): void
    {
        Schema::table('history_penyaluran', function (Blueprint $table) {
            $table->dropUnique('history_program_tipe_unique');
        });
    }
};
