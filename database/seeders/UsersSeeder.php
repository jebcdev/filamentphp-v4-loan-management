<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Admin */
        User::create([
            'name'=>'Admin User',
            'email'=>'admin@admin.com',
            'password'=>bcrypt('123456'),
            'role'=>Role::admin->value,
        ]);
        /* Admin */
    }
}
