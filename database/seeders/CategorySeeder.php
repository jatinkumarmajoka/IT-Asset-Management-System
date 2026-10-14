<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Computers and IT Equipment',
                'description' => 'Desktop computers, laptops, workstations, monitors, servers, keyboards, mice and UPS.',
            ],
            [
                'name' => 'Networking and Communication',
                'description' => 'Routers, network switches, Wi-Fi access points, firewalls and IP phones.',
            ],
            [
                'name' => 'Printing and Scanning',
                'description' => 'Printers, scanners, photocopiers and plotters.',
            ],
            [
                'name' => 'CCTV and Security',
                'description' => 'CCTV cameras, DVRs, NVRs, biometric devices and access control equipment.',
            ],
            [
                'name' => 'Display and Presentation',
                'description' => 'LED TVs, projectors, interactive displays and smart boards.',
            ],
            [
                'name' => 'Electrical and Other Equipment',
                'description' => 'Air conditioners, fans, inverters, batteries, generators and water coolers.',
            ],
        ];

        $columns = Schema::getColumnListing('categories');

        foreach ($categories as $category) {
            $exists = DB::table('categories')
                ->where('name', $category['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            $record = ['name' => $category['name']];

            if (in_array('description', $columns)) {
                $record['description'] = $category['description'];
            }

            if (in_array('status', $columns)) {
                $record['status'] = 'active';
            }

            if (in_array('created_at', $columns)) {
                $record['created_at'] = now();
            }

            if (in_array('updated_at', $columns)) {
                $record['updated_at'] = now();
            }

            DB::table('categories')->insert($record);
        }
    }
}
