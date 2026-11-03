<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Umbrella categories (parents)
        $umbrellaCategories = [
            [
                'name' => 'Computers & Peripherals',
                'description' => 'Desktop computers, laptops, servers, monitors, keyboards, mice, docking stations, webcams, and related peripherals.',
            ],
            [
                'name' => 'Networking & Communication',
                'description' => 'Routers, switches, access points, firewalls, load balancers, wireless controllers, conference phones, and communication equipment.',
            ],
            [
                'name' => 'Cables & Accessories',
                'description' => 'Power cables, network cables, USB cables, adapters, connectors, KVM switches, and related accessories.',
            ],
            [
                'name' => 'Printing & Scanning',
                'description' => 'Printers, scanners, fax machines, plotters, and related imaging equipment.',
            ],
            [
                'name' => 'Audio/Video & Presentation',
                'description' => 'Projectors, speakers, microphones, headsets, video conferencing systems, TVs, video walls, interactive panels, and presentation equipment.',
            ],
            [
                'name' => 'Power & Backup',
                'description' => 'UPS systems, inverters, stabilizers, generators, batteries, solar power systems, and power backup equipment.',
            ],
            [
                'name' => 'Furniture & Fixtures',
                'description' => 'Office furniture (chairs, tables, desks, cabinets), racks, lecture stands, and fixtures.',
            ],
            [
                'name' => 'Appliances',
                'description' => 'Air conditioners, chillers, HVAC systems, fans, refrigerators, and other appliances.',
            ],
            [
                'name' => 'Storage & Media',
                'description' => 'Storage devices (NAS, SAN), external drives, HDD, SSD, pen drives, and media storage.',
            ],
            [
                'name' => 'Other Equipment',
                'description' => 'Equipment that does not fit into the above categories (CCTV, security, medical, lab, software, mobile devices, etc.).',
            ],
        ];

        $columns = Schema::getColumnListing('categories');

        // Create umbrella categories first
        $umbrellaCategoryIds = [];
        foreach ($umbrellaCategories as $category) {
            $exists = Category::where('name', $category['name'])->exists();

            if ($exists) {
                $existing = Category::where('name', $category['name'])->first();
                $umbrellaCategoryIds[$category['name']] = $existing->id;
                continue;
            }

            $record = ['name' => $category['name'], 'parent_id' => null];

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

            $created = Category::create($record);
            $umbrellaCategoryIds[$category['name']] = $created->id;
        }

        // Fine-grained categories (children) mapped to umbrella categories
        $fineGrainedCategories = [
            // Computers & Peripherals
            ['name' => 'Desktop', 'description' => 'Desktop computers and workstations', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Laptop', 'description' => 'Portable computers and notebooks', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Server', 'description' => 'Server hardware (rack, tower, blade)', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Monitor', 'description' => 'Display monitors (LED, LCD)', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Keyboard', 'description' => 'Keyboards', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Mouse', 'description' => 'Mice and pointing devices', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Webcam', 'description' => 'Web cameras', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Headset', 'description' => 'Headsets and headphones', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Speaker', 'description' => 'Speakers and audio output devices', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Microphone', 'description' => 'Microphones', 'parent' => 'Computers & Peripherals'],
            ['name' => 'Docking Station', 'description' => 'Docking stations and port replicators', 'parent' => 'Computers & Peripherals'],
            ['name' => 'KVM Switch', 'description' => 'KVM switches', 'parent' => 'Computers & Peripherals'],

            // Networking & Communication
            ['name' => 'Switch', 'description' => 'Network switches (POE, managed, unmanaged)', 'parent' => 'Networking & Communication'],
            ['name' => 'Router', 'description' => 'Network routers', 'parent' => 'Networking & Communication'],
            ['name' => 'Firewall', 'description' => 'Network firewalls and security appliances', 'parent' => 'Networking & Communication'],
            ['name' => 'Load Balancer', 'description' => 'Network load balancers', 'parent' => 'Networking & Communication'],
            ['name' => 'Access Point', 'description' => 'Wireless access points', 'parent' => 'Networking & Communication'],
            ['name' => 'Wireless Controller', 'description' => 'Wireless LAN controllers', 'parent' => 'Networking & Communication'],
            ['name' => 'Conference Phone', 'description' => 'Conference phones', 'parent' => 'Networking & Communication'],

            // Cables & Accessories (new categories for items that don't exist yet)
            // Note: These will be created if items like "cable", "adapter" are imported

            // Printing & Scanning
            ['name' => 'Printer', 'description' => 'Printers (laser, inkjet, multifunction)', 'parent' => 'Printing & Scanning'],
            ['name' => 'Scanner', 'description' => 'Document and image scanners', 'parent' => 'Printing & Scanning'],
            ['name' => 'Fax Machine', 'description' => 'Fax machines', 'parent' => 'Printing & Scanning'],
            ['name' => 'Plotter', 'description' => 'Large format plotters', 'parent' => 'Printing & Scanning'],

            // Audio/Video & Presentation
            ['name' => 'Projector', 'description' => 'Projectors', 'parent' => 'Audio/Video & Presentation'],
            ['name' => 'TV', 'description' => 'Televisions and smart TVs', 'parent' => 'Audio/Video & Presentation'],
            ['name' => 'Video Wall', 'description' => 'Video wall displays', 'parent' => 'Audio/Video & Presentation'],
            ['name' => 'Interactive Panel', 'description' => 'Interactive panels, smart boards, whiteboards', 'parent' => 'Audio/Video & Presentation'],
            ['name' => 'Video Conferencing', 'description' => 'Video conferencing systems', 'parent' => 'Audio/Video & Presentation'],
            ['name' => 'Audio Equipment', 'description' => 'Audio equipment (amplifiers, mixers)', 'parent' => 'Audio/Video & Presentation'],

            // Power & Backup
            ['name' => 'UPS', 'description' => 'Uninterruptible power supplies and battery backups', 'parent' => 'Power & Backup'],
            ['name' => 'Inverter', 'description' => 'Power inverters', 'parent' => 'Power & Backup'],
            ['name' => 'Stabilizer', 'description' => 'Voltage stabilizers', 'parent' => 'Power & Backup'],
            ['name' => 'Generator', 'description' => 'Generators and DG sets', 'parent' => 'Power & Backup'],
            ['name' => 'Solar Power', 'description' => 'Solar panels and solar power systems', 'parent' => 'Power & Backup'],

            // Furniture & Fixtures
            ['name' => 'Furniture', 'description' => 'Office furniture (chairs, tables, desks, cabinets)', 'parent' => 'Furniture & Fixtures'],
            ['name' => 'Rack', 'description' => 'Server and network racks', 'parent' => 'Furniture & Fixtures'],

            // Appliances
            ['name' => 'Air Conditioner', 'description' => 'Air conditioners (split, window, cassette, VRF)', 'parent' => 'Appliances'],
            ['name' => 'Chiller', 'description' => 'Chillers', 'parent' => 'Appliances'],
            ['name' => 'HVAC', 'description' => 'HVAC systems', 'parent' => 'Appliances'],

            // Storage & Media
            ['name' => 'Storage Device', 'description' => 'NAS, SAN, HDD, SSD, external drives', 'parent' => 'Storage & Media'],

            // Other Equipment
            ['name' => 'CCTV', 'description' => 'CCTV cameras (IP, dome, bullet, PTZ)', 'parent' => 'Other Equipment'],
            ['name' => 'NVR', 'description' => 'Network video recorders', 'parent' => 'Other Equipment'],
            ['name' => 'DVR', 'description' => 'Digital video recorders', 'parent' => 'Other Equipment'],
            ['name' => 'Access Control', 'description' => 'Access control systems and card readers', 'parent' => 'Other Equipment'],
            ['name' => 'Biometric Device', 'description' => 'Biometric and fingerprint devices', 'parent' => 'Other Equipment'],
            ['name' => 'Software', 'description' => 'Software applications', 'parent' => 'Other Equipment'],
            ['name' => 'Software License', 'description' => 'Software licenses and subscriptions', 'parent' => 'Other Equipment'],
            ['name' => 'Mobile Device', 'description' => 'Mobile phones and smartphones', 'parent' => 'Other Equipment'],
            ['name' => 'Tablet', 'description' => 'Tablets and iPads', 'parent' => 'Other Equipment'],
            ['name' => 'Medical Equipment', 'description' => 'Medical equipment (monitors, ventilators, ECG, ultrasound, X-ray, CT, MRI)', 'parent' => 'Other Equipment'],
            ['name' => 'Lab Equipment', 'description' => 'Laboratory equipment (centrifuges, microscopes, spectrophotometers, autoclaves, incubators, refrigerators, balances, pH meters, etc.)', 'parent' => 'Other Equipment'],
        ];

        foreach ($fineGrainedCategories as $category) {
            $parentId = $umbrellaCategoryIds[$category['parent']] ?? null;

            if (!$parentId) {
                continue; // Skip if parent not found
            }

            $exists = Category::where('name', $category['name'])->exists();

            if ($exists) {
                $existing = Category::where('name', $category['name'])->first();
                // Update parent_id if not set
                if ($existing->parent_id === null) {
                    $existing->update(['parent_id' => $parentId]);
                }
                continue;
            }

            $record = [
                'name' => $category['name'],
                'parent_id' => $parentId,
            ];

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

            Category::create($record);
        }
    }
}
