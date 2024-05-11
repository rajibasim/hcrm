<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::firstOrCreate(['email' => 'admin@admin.com'], ['name' => 'Admin', 'email' => 'admin@admin.com', 'password' => Hash::make('123456'),]);
        if (count($user->getRoleNames()) < 1) {
            $superAdminRole = Role::findByName('super_admin');
            $user->syncRoles([$superAdminRole]);
        }
    }
}
