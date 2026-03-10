<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['role_id' => 1, 'role_name' => 'Direktur Utama'],
            ['role_id' => 2, 'role_name' => 'Direksi'],
            ['role_id' => 3, 'role_name' => 'Manager'],
            ['role_id' => 4, 'role_name' => 'Pegawai'],
        ]);
    }
}
