<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawaran_bantuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_instansi', 100);
            $table->string('pj_donatur', 100);
            $table->string('jabatan_donatur', 100);
            $table->string('kontak_donatur', 20);
            $table->string('jenis_penawaran', 50);
            $table->text('detail_bantuan');
            $table->string('dokumen_donatur');
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->boolean('is_funded')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penawaran_bantuan');
    }
};
