<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_bantuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_pj', 100);
            $table->string('jabatan', 100);
            $table->enum('target_bantuan', ['warga', 'fasilitas']);
            $table->integer('jumlah_kk')->nullable();
            $table->string('provinsi', 50);
            $table->string('kota', 50);
            $table->string('kecamatan', 50);
            $table->string('desa', 50);
            $table->text('alasan');
            $table->string('dokumen_desa');
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->boolean('is_funded')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_bantuan');
    }
};
