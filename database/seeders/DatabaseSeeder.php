<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@sibantal.com'],
            [
                'nama_lengkap' => 'Admin SI BanTal',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'asal_desa' => null,
                'nama_organisasi' => null,
                'created_at' => now(),
            ]
        );
    }
}
