<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $permissionGroups = PermissionGroup::orderByRaw('CONVERT(id, SIGNED) asc')->get();
        
        $permissions = [];
        $counterForUUID = 1;
        foreach ($permissionGroups as $permissionGroup) {
           foreach (['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'] as $name => $label) {
                $permission = [
                    'id' => $counterForUUID,
                    'label' => $label,
                    'name' => "{$permissionGroup->name}_{$name}",
                    'group_id' => $permissionGroup->id,
                    'guard_name' => 'web',
                ];
                array_push($permissions, $permission);
                $counterForUUID++;
            }
        }

        foreach ($permissions as $permission) {
            $id = $permission['id'];
            unset($permission['id']);
            Permission::firstOrCreate(['id' => $id], $permission);
        }
    }
}
