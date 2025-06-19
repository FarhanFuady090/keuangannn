<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role')->insert([
            'peran_user' => 'admin',
        ]);

        DB::table('role')->insert([
            'peran_user' => 'yayasan',
        ]);

        DB::table('role')->insert([
            'peran_user' => 'tupusat',
        ]);

        DB::table('role')->insert([
            'peran_user' => 'tuunit',
        ]);
    }
}