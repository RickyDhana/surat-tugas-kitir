<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        DB::table('users')->insert([
            [
                'nama' => 'Admin',
                'username' => 'admin123',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'nama' => 'Kepala Biro',
                'username' => 'kabiro123',
                'password' => Hash::make('kabiro123'),
                'role' => 'kepala_biro',
            ],
            [
                'nama' => 'Penera Candra',
                'username' => 'penera_candra',
                'password' => Hash::make('penera1'),
                'role' => 'penera',
            ],
            [
                'nama' => 'Penera Rizky',
                'username' => 'penera_rizky',
                'password' => Hash::make('penera2'),
                'role' => 'penera',
            ],
            [
                'nama' => 'Penera Rino',
                'username' => 'penera_rino',
                'password' => Hash::make('penera3'),
                'role' => 'penera',
            ],
        ]);
    }
}
