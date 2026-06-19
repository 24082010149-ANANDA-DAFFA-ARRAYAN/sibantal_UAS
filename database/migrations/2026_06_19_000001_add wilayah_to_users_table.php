<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'provinsi')) {
                $table->string('provinsi', 100)->nullable()->after('asal_desa');
            }
            if (!Schema::hasColumn('users', 'kota')) {
                $table->string('kota', 100)->nullable()->after('provinsi');
            }
            if (!Schema::hasColumn('users', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('kota');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'provinsi')) $table->dropColumn('provinsi');
            if (Schema::hasColumn('users', 'kota')) $table->dropColumn('kota');
            if (Schema::hasColumn('users', 'kecamatan')) $table->dropColumn('kecamatan');
        });
    }
};