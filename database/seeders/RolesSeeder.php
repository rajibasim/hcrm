<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['id' => '1'], ['name' => 'super_admin', 'label' => 'Super Admin', 'created_by' => '1', 'updated_by' => '1']);
        $permissions = Permission::get();
        $role->syncPermissions($permissions);
    }
}
