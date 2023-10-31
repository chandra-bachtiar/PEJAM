<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Calon;
use App\Models\JenisKelamin;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'nis' => '1234567890',
            'username' => 'admin',
            'nama' => 'Administrator',
            'jenis_kelamin' => 'L',
            'jurusan' => 'Administrasi Bisnis',
            'kelas' => 'XII',
            'role' => 'Admin',
            'status' => 'Guru',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
