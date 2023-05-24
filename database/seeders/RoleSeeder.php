<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role' => 'superAdmin',
            ],
            [
                'role' => 'Employee',
            ],
            [
                'role' => 'humanResource',
            ],
        ];

        Role::insert($roles);
    }
}