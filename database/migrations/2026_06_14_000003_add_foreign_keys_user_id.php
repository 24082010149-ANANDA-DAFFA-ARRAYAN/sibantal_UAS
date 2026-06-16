<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_bantuan', function (Blueprint $table) {
            $table->foreign('user_id', 'permintaan_user_fk')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('penawaran_bantuan', function (Blueprint $table) {
            $table->foreign('user_id', 'penawaran_user_fk')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('history_penyaluran', function (Blueprint $table) {
            $table->foreign('user_id', 'history_user_fk')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_bantuan', function (Blueprint $table) {
            $table->dropForeign('permintaan_user_fk');
        });

        Schema::table('penawaran_bantuan', function (Blueprint $table) {
            $table->dropForeign('penawaran_user_fk');
        });

        Schema::table('history_penyaluran', function (Blueprint $table) {
            $table->dropForeign('history_user_fk');
        });
    }
};
