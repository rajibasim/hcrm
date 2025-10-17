<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryStatus;
use Illuminate\Support\Str;

class DeliveryStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
        	'Pending',
            'Delivery',
            'Delivery Pending',
            'Return',
            'Partial Return',
            'HB Delivery',
        ];

        foreach ($statuses as $status) {
            DeliveryStatus::create([
                'name'        => $status,
            ]);
        }
    }
}
