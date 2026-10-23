<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::create([
            'name' => 'Information Technology',
            'code' => 'IT',
            'description' => 'IT Department',
            'status' => 'active',
        ]);

        $cs = Department::create([
            'name' => 'Computer Science',
            'code' => 'CSE',
            'description' => 'Computer Science Department',
            'status' => 'active',
        ]);

        $desktop = Category::create(['name' => 'Desktop', 'description' => 'Desktop computers', 'status' => 'active']);
        $laptop = Category::create(['name' => 'Laptop', 'description' => 'Portable computers', 'status' => 'active']);
        $printer = Category::create(['name' => 'Printer', 'description' => 'Printing devices', 'status' => 'active']);
        $monitor = Category::create(['name' => 'Monitor', 'description' => 'Display devices', 'status' => 'active']);

        $employee = Employee::create([
            'employee_code' => 'EMP-001',
            'name' => 'Demo Employee',
            'designation' => 'System Administrator',
            'department_id' => $it->id,
            'email' => 'admin@example.test',
            'status' => 'active',
        ]);

        Asset::create([
            'asset_code' => 'IT-000001',
            'department_id' => $it->id,
            'category_id' => $desktop->id,
            'item_name' => 'Desktop Computer',
            'make' => 'Dell',
            'model' => 'OptiPlex',
            'issued_quantity' => 1,
            'physically_verified_quantity' => 1,
            'present_occupant_id' => $employee->id,
            'asset_serial_number' => 'DEMO-SERIAL-001',
            'barcode_number' => 'IT-BC-000001',
            'asset_status' => 'Assigned',
            'condition' => 'Good',
            'location' => 'IT Lab',
            'remarks' => 'Development seed record',
        ]);

        Asset::create([
            'asset_code' => 'CSE-000001',
            'department_id' => $cs->id,
            'category_id' => $laptop->id,
            'item_name' => 'Laptop',
            'make' => 'HP',
            'model' => 'EliteBook',
            'issued_quantity' => 1,
            'physically_verified_quantity' => 0,
            'asset_status' => 'Unassigned',
            'condition' => 'Good',
            'location' => 'Store Room',
        ]);
    }
}
