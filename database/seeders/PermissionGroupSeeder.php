<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Caution: Please don't change the sequence of array items.
        $permissionGroups = [
            [
                'name' => 'user',
                'label' => 'User'
            ],
            [
                'name' => 'role',
                'label' => 'Role'
            ],
            [
                'name' => 'customer',
                'label' => 'Customer'
            ],
            [
                'name' => 'sales_person',
                'label' => 'Sales Person'
            ],
            [
                'name' => 'bill_entry',
                'label' => 'Bill Entry'
            ],
            [
                'name' => 'status',
                'label' => 'Status'
            ],
            [
                'name' => 'report',
                'label' => 'Report'
            ]
        ];

        $counterForUUID = 1;
        foreach ($permissionGroups as $key => $permissionGroup) {
            $permissionGroups[$key]['id'] = $counterForUUID;
            $counterForUUID++;
        }

        foreach ($permissionGroups as $permissionGroup) {
            $id = $permissionGroup['id'];
            unset($permissionGroup['id']);
            PermissionGroup::firstOrCreate(['id' => $id], $permissionGroup);
        }
    }
}
