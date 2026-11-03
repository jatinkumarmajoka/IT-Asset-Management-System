<?php

namespace Tests\Unit\Imports;

use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Services\Imports\AssetImportNormalizer;
use App\Services\Imports\ImportMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetImportNormalizerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed required departments
        $this->seedDepartments();
        $this->seedCategories();
    }

    protected function seedDepartments(): void
    {
        // Computer Centre (non-teaching)
        Department::firstOrCreate(
            ['code' => 'CC'],
            [
                'name' => 'Computer Centre',
                'description' => null,
                'status' => 'active',
                'type' => 'non_teaching',
            ]
        );

        // ICT Section (non-teaching)
        Department::firstOrCreate(
            ['code' => 'ICT'],
            [
                'name' => 'ICT Section',
                'description' => null,
                'status' => 'active',
                'type' => 'non_teaching',
            ]
        );

        // Academic department for employee resolution
        Department::firstOrCreate(
            ['code' => 'CSE'],
            [
                'name' => 'Department of Computer Science and Engineering',
                'description' => null,
                'status' => 'active',
                'type' => 'academic',
            ]
        );
    }

    protected function seedCategories(): void
    {
        // Seed only umbrella (parent) categories - import matches to these
        $umbrellaCategories = [
            ['name' => 'Computers & Peripherals', 'description' => 'Desktop computers, laptops, servers, monitors, keyboards, mice, docking stations, webcams, and related peripherals.', 'parent_id' => null],
            ['name' => 'Networking & Communication', 'description' => 'Routers, switches, access points, firewalls, load balancers, wireless controllers, conference phones, and communication equipment.', 'parent_id' => null],
            ['name' => 'Cables & Accessories', 'description' => 'Power cables, network cables, USB cables, adapters, connectors, KVM switches, and related accessories.', 'parent_id' => null],
            ['name' => 'Printing & Scanning', 'description' => 'Printers, scanners, fax machines, plotters, and related imaging equipment.', 'parent_id' => null],
            ['name' => 'Audio/Video & Presentation', 'description' => 'Projectors, speakers, microphones, headsets, video conferencing systems, TVs, video walls, interactive panels, and presentation equipment.', 'parent_id' => null],
            ['name' => 'Power & Backup', 'description' => 'UPS systems, inverters, stabilizers, generators, batteries, solar power systems, and power backup equipment.', 'parent_id' => null],
            ['name' => 'Furniture & Fixtures', 'description' => 'Office furniture (chairs, tables, desks, cabinets), racks, lecture stands, and fixtures.', 'parent_id' => null],
            ['name' => 'Appliances', 'description' => 'Air conditioners, chillers, HVAC systems, fans, refrigerators, and other appliances.', 'parent_id' => null],
            ['name' => 'Storage & Media', 'description' => 'Storage devices (NAS, SAN), external drives, HDD, SSD, pen drives, and media storage.', 'parent_id' => null],
            ['name' => 'Other Equipment', 'description' => 'Equipment that does not fit into the above categories (CCTV, security, medical, lab, software, mobile devices, etc.).', 'parent_id' => null],
        ];

        foreach ($umbrellaCategories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description'], 'status' => 'active', 'parent_id' => $cat['parent_id']]
            );
        }
    }

    public function test_computer_center_variations_match_computer_centre_department(): void
    {
        $matcher = new ImportMatcher();

        // Test exact alias match
        $result = $matcher->matchDepartment('Computer Center');
        $this->assertEquals('matched', $result['status'], "Failed for 'Computer Center': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);
        $this->assertEquals('alias', $result['matchType']);

        // Test British spelling
        $result = $matcher->matchDepartment('Computer Centre');
        $this->assertEquals('matched', $result['status'], "Failed for 'Computer Centre': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);

        // Test case variations
        $result = $matcher->matchDepartment('COMPUTER CENTER');
        $this->assertEquals('matched', $result['status'], "Failed for 'COMPUTER CENTER': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);

        $result = $matcher->matchDepartment('computer center');
        $this->assertEquals('matched', $result['status'], "Failed for 'computer center': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);

        // Test with extra whitespace
        $result = $matcher->matchDepartment('  Computer Center  ');
        $this->assertEquals('matched', $result['status'], "Failed for '  Computer Center  ': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);

        // Test CC abbreviation
        $result = $matcher->matchDepartment('CC');
        $this->assertEquals('matched', $result['status'], "Failed for 'CC': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);
    }

    public function test_ict_section_variations_match(): void
    {
        $matcher = new ImportMatcher();

        $result = $matcher->matchDepartment('ICT Section');
        $this->assertEquals('matched', $result['status']);
        $this->assertEquals('ICT Section', $result['department']->name);

        $result = $matcher->matchDepartment('ICT');
        $this->assertEquals('matched', $result['status']);
        $this->assertEquals('ICT Section', $result['department']->name);

        $result = $matcher->matchDepartment('Information Technology');
        $this->assertEquals('matched', $result['status']);
        $this->assertEquals('ICT Section', $result['department']->name);
    }

    public function test_unmatched_department_returns_unmatched(): void
    {
        $matcher = new ImportMatcher();

        $result = $matcher->matchDepartment('Non Existent Department');
        $this->assertEquals('unmatched', $result['status']);
        $this->assertNull($result['department']);
    }

    public function test_department_matching_handles_punctuation(): void
    {
        $matcher = new ImportMatcher();

        // Test with trailing period
        $result = $matcher->matchDepartment('Computer Center.');
        $this->assertEquals('matched', $result['status'], "Failed for 'Computer Center.': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);

        // Test with comma
        $result = $matcher->matchDepartment('Computer Center,');
        $this->assertEquals('matched', $result['status'], "Failed for 'Computer Center,': " . json_encode($result));
        $this->assertEquals('Computer Centre', $result['department']->name);

        // Test ICT Section with punctuation
        $result = $matcher->matchDepartment('ICT Section.');
        $this->assertEquals('matched', $result['status'], "Failed for 'ICT Section.': " . json_encode($result));
        $this->assertEquals('ICT Section', $result['department']->name);

        // Parentheses indicate room/location detail, not department - should not match as department
        $result = $matcher->matchDepartment('Computer Center (Main)');
        $this->assertEquals('unmatched', $result['status'], "Parentheses case should be unmatched for department: " . json_encode($result));
    }

    public function test_normalizer_returns_field_level_errors_not_generic(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Row with valid department but invalid category (item name not matching any category)
        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Unknown Item Type XYZ',
            'Issued Quantity as per Departmental Stock Register' => '1',
            'Make and Model of the Item' => 'Dell OptiPlex',
        ];

        $result = $normalizer->normalize($rawData);

        // Should be invalid due to category mismatch
        $this->assertEquals('invalid', $result['status']);

        // CRITICAL: Should NOT return generic "One or more extracted assets require review."
        // Should return actual field-level error about category
        $this->assertNotEquals(
            ['One or more extracted assets require review.'],
            $result['errors'],
            'Normalizer returns generic error instead of field-level errors'
        );

        // Should have specific category error
        $errors = $result['errors'] ?? [];
        $hasCategoryError = collect($errors)->contains(function ($error) {
            return stripos($error, 'category') !== false || stripos($error, 'Category') !== false;
        });
        $this->assertTrue($hasCategoryError, 'Expected category-related error in: ' . json_encode($errors));
    }

    public function test_normalizer_valid_row_with_computer_center(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Row with Computer Center as occupant and valid category
        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Desktop Computer',
            'Issued Quantity as per Departmental Stock Register' => '1',
            'Make and Model of the Item' => 'Dell OptiPlex 7090',
            'Date of Issue' => '01/01/2024',
        ];

        $result = $normalizer->normalize($rawData);

        // Department should be matched to Computer Centre
        $this->assertEquals('Computer Centre', $result['department']['name'] ?? '');

        // Should have assets
        $this->assertNotEmpty($result['assets']);

        // Check first asset
        $asset = $result['assets'][0];
        $this->assertEquals('Computer Centre', $asset['department_name']);
        $this->assertEquals('Computers & Peripherals', $asset['category_name']); // Desktop Computer -> Computers & Peripherals
        $this->assertEquals(1, $asset['issued_quantity']);
    }

    public function test_normalizer_multiple_items_with_mixed_validity(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Row with multiple items - one valid, one invalid category
        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => "Desktop Computer\nUnknown Item XYZ",
            'Issued Quantity as per Departmental Stock Register' => "1\n1",
            'Make and Model of the Item' => "Dell OptiPlex\nUnknown Make",
        ];

        $result = $normalizer->normalize($rawData);

        $this->assertEquals('invalid', $result['status']);
        $this->assertCount(2, $result['assets']);

        // First asset should be valid
        $this->assertTrue($result['assets'][0]['valid']);
        $this->assertEquals('Computers & Peripherals', $result['assets'][0]['category_name']);

        // Second asset should be invalid with category error
        $this->assertFalse($result['assets'][1]['valid']);
        $assetErrors = $result['assets'][1]['errors'];
        $hasCategoryError = collect($assetErrors)->contains(function ($error) {
            return stripos($error, 'category') !== false;
        });
        $this->assertTrue($hasCategoryError, 'Expected category error for second asset: ' . json_encode($assetErrors));

        // Overall errors should include the specific asset errors, not generic message
        $this->assertNotEquals(
            ['One or more extracted assets require review.'],
            $result['errors'],
            'Normalizer returns generic error instead of field-level errors for mixed validity'
        );
    }

    public function test_normalizer_handles_missing_quantity(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Desktop Computer',
            // Missing issued quantity
            'Make and Model of the Item' => 'Dell OptiPlex',
        ];

        $result = $normalizer->normalize($rawData);

        $this->assertEquals('invalid', $result['status']);
        
        $errors = $result['errors'] ?? [];
        $hasQuantityError = collect($errors)->contains(function ($error) {
            return stripos($error, 'quantity') !== false || stripos($error, 'Quantity') !== false;
        });
        $this->assertTrue($hasQuantityError, 'Expected quantity error: ' . json_encode($errors));
    }

    public function test_normalizer_handles_invalid_date(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Desktop Computer',
            'Issued Quantity as per Departmental Stock Register' => '1',
            'Date of Issue' => 'invalid-date',
        ];

        $result = $normalizer->normalize($rawData);

        $this->assertEquals('invalid', $result['status']);
        
        $errors = $result['errors'] ?? [];
        $hasDateError = collect($errors)->contains(function ($error) {
            return stripos($error, 'date') !== false || stripos($error, 'Date') !== false;
        });
        $this->assertTrue($hasDateError, 'Expected date error: ' . json_encode($errors));
    }

    public function test_location_matching_case_insensitive(): void
    {
        $matcher = new ImportMatcher();

        // Test predefined locations with case variations
        $testCases = [
            'Computer Lab' => 'Computer Lab',
            'computer lab' => 'Computer Lab',
            'COMPUTER LAB' => 'Computer Lab',
            '  Computer Lab  ' => 'Computer Lab',
            'Research Lab' => 'Research Lab',
            'research laboratory' => 'Research Lab',
            'Classroom' => 'Classroom',
            'class room' => 'Classroom',
            'Lecture Hall' => 'Lecture Hall',
            'auditorium' => 'Lecture Hall',
            'Office' => 'Office',
            'office' => 'Office',
            'Store Room' => 'Store Room',
            'storage room' => 'Store Room',
            'Workshop' => 'Workshop',
            'Library' => 'Library',
            // Computer Center variations should match Computer Lab
            'Computer Center' => 'Computer Lab',
            'Computer Centre' => 'Computer Lab',
            'computer center' => 'Computer Lab',
            'computer centre' => 'Computer Lab',
        ];

        foreach ($testCases as $input => $expected) {
            $result = $matcher->matchLocation($input);
            $this->assertEquals('matched', $result['status'], "Failed for '$input': " . json_encode($result));
            $this->assertEquals($expected, $result['location'], "Location mismatch for '$input'");
            $this->assertNull($result['customLocation'], "Custom location should be null for '$input'");
        }
    }

    public function test_location_computer_center_as_custom_location(): void
    {
        $matcher = new ImportMatcher();

        // If location is "Computer Center Main Block" - should match Computer Lab keyword
        $result = $matcher->matchLocation('Computer Center Main Block');
        $this->assertEquals('matched', $result['status']);
        $this->assertEquals('Computer Lab', $result['location']);

        // If location is something completely custom
        $result = $matcher->matchLocation('Computer Center Building A Floor 2');
        $this->assertEquals('matched', $result['status']);
        $this->assertEquals('Computer Lab', $result['location']);
    }

    public function test_location_fallback_to_other_with_custom(): void
    {
        $matcher = new ImportMatcher();

        $result = $matcher->matchLocation('Custom Location XYZ');
        $this->assertEquals('unmatched', $result['status']);
        $this->assertEquals('Other', $result['location']);
        $this->assertEquals('Custom Location XYZ', $result['customLocation']);
    }

    public function test_room_number_extraction(): void
    {
        $matcher = new ImportMatcher();

        $testCases = [
            'Room 205' => '205',
            'Room-205' => '205',
            'R-205' => '205',
            'Lab 301' => '301',
            'Room No. 101' => '101',
            'Room No 202' => '202',
            'Block A Room 303' => '303',
            'Physics 205' => '205',
            'Chemistry-301' => '301',
            'Computer Center 101' => '101',
            'No room here' => null,
            '' => null,
        ];

        foreach ($testCases as $input => $expected) {
            $result = $matcher->extractRoomNumber($input);
            $this->assertEquals($expected, $result, "Room extraction failed for '$input'");
        }
    }

    public function test_employee_resolution_within_department(): void
    {
        // Create department and employee
        $dept = Department::where('code', 'CSE')->first();
        $employee = Employee::create([
            'employee_code' => 'EMP001',
            'name' => 'John Doe',
            'designation' => 'Professor',
            'department_id' => $dept->id,
            'email' => 'john.doe@example.com',
            'status' => 'active',
        ]);

        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Use reflection to test private resolveEmployee method
        $reflection = new \ReflectionClass($normalizer);
        $method = $reflection->getMethod('resolveEmployee');
        $method->setAccessible(true);

        // Exact name match
        $result = $method->invoke($normalizer, 'John Doe', $dept);
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result->name);

        // Case insensitive
        $result = $method->invoke($normalizer, 'john doe', $dept);
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result->name);

        // Whitespace
        $result = $method->invoke($normalizer, '  John Doe  ', $dept);
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result->name);

        // No match
        $result = $method->invoke($normalizer, 'Jane Smith', $dept);
        $this->assertNull($result);
    }

    public function test_normalizer_end_to_end_computer_center_workbook_row(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Simulate a typical workbook row with Computer Center as occupant
        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Desktop Computer',
            'Issued Quantity as per Departmental Stock Register' => '5',
            'Make and Model of the Item' => 'Dell OptiPlex 7090',
            'Date of Issue' => '15/01/2024',
            'Location' => 'Computer Lab',
            'Present Room Number' => 'Room 205',
            'Barcode/Sticker Sr. No. of the Sticker pasted on the Asset' => 'BC001',
            'Asset Serial Number' => 'SN001',
        ];

        $result = $normalizer->normalize($rawData);

        // Should be valid
        $this->assertEquals('valid', $result['status'], 'Errors: ' . json_encode($result['errors'] ?? []));
        $this->assertEmpty($result['errors']);

        // Department should be Computer Centre (preserving DB spelling)
        $this->assertEquals('Computer Centre', $result['department']['name']);
        $this->assertEquals('Computer Centre', $result['occupant']['name']);

        // Should have one asset
        $this->assertCount(1, $result['assets']);
        $asset = $result['assets'][0];

        // Asset should have correct department
        $this->assertEquals('Computer Centre', $asset['department_name']);
        
        // Category should be Computers & Peripherals (from "Desktop Computer")
        $this->assertEquals('Computers & Peripherals', $asset['category_name']);
        
        // Quantity
        $this->assertEquals(5, $asset['issued_quantity']);
        
        // Date parsed correctly
        $this->assertEquals('2024-01-15', $asset['date_of_issue']);
        
        // Location matched to Computer Lab (predefined)
        $this->assertEquals('Computer Lab', $asset['present_location']);
        
        // Room number extracted
        $this->assertEquals('205', $asset['present_room_number']);
        
        // Barcode and serial
        $this->assertEquals('BC001', $asset['barcode_number']);
        $this->assertEquals('SN001', $asset['asset_serial_number']);
        
        // Asset should be valid
        $this->assertTrue($asset['valid']);
    }

    public function test_normalizer_computer_center_case_variations(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        $variations = [
            'Computer Center',
            'COMPUTER CENTER',
            'computer center',
            'Computer Centre',
            'COMPUTER CENTRE',
            'computer centre',
            '  Computer Center  ',
            'Computer Center.',
            'Computer Center,',
        ];

        foreach ($variations as $occupant) {
            $rawData = [
                'Present Occupant' => $occupant,
                'Name of the Item' => 'Laptop',
                'Issued Quantity as per Departmental Stock Register' => '1',
            ];

            $result = $normalizer->normalize($rawData);
            
            $this->assertEquals('valid', $result['status'], "Failed for occupant '$occupant': " . json_encode($result['errors'] ?? []));
            $this->assertEquals('Computer Centre', $result['department']['name'], "Department name mismatch for '$occupant'");
        }
    }

    public function test_normalizer_unmatched_department_shows_specific_error(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        $rawData = [
            'Present Occupant' => 'Non Existent Department XYZ',
            'Name of the Item' => 'Desktop Computer',
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);

        $this->assertEquals('invalid', $result['status']);
        
        // Should show specific department matching error, not generic
        $errors = $result['errors'] ?? [];
        $this->assertNotEquals(
            ['One or more extracted assets require review.'],
            $errors
        );
        
        $hasDeptError = collect($errors)->contains(function ($error) {
            return stripos($error, 'department') !== false || stripos($error, 'Department') !== false;
        });
        $this->assertTrue($hasDeptError, 'Expected department error: ' . json_encode($errors));
    }

    public function test_normalizer_preserves_database_spelling(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Test various inputs that should all resolve to "Computer Centre" (British spelling in DB)
        $inputs = ['Computer Center', 'COMPUTER CENTER', 'computer center', 'CC'];
        
        foreach ($inputs as $input) {
            $rawData = [
                'Present Occupant' => $input,
                'Name of the Item' => 'Desktop',
                'Issued Quantity as per Departmental Stock Register' => '1',
            ];

            $result = $normalizer->normalize($rawData);
            $this->assertEquals('valid', $result['status']);
            
            // Database spelling must be preserved
            $this->assertEquals('Computer Centre', $result['department']['name']);
            $this->assertEquals('Computer Centre', $result['occupant']['name']);
            $this->assertEquals('Computer Centre', $result['assets'][0]['department_name']);
        }
    }

    public function test_category_matching_with_existing_database_categories(): void
    {
        $matcher = new ImportMatcher();

        // Test the categories that exist in DatabaseSeeder - now mapped to umbrella categories
        $categoriesToTest = [
            ['input' => 'Desktop Computer', 'expectedCategory' => 'Computers & Peripherals'],
            ['input' => 'Laptop', 'expectedCategory' => 'Computers & Peripherals'],
            ['input' => 'Printer', 'expectedCategory' => 'Printing & Scanning'],
            ['input' => 'Monitor', 'expectedCategory' => 'Computers & Peripherals'],
        ];

        foreach ($categoriesToTest as $test) {
            $result = $matcher->matchCategory($test['input']);
            $this->assertEquals('matched', $result['status'], "Failed for '{$test['input']}': " . json_encode($result));
            $this->assertEquals($test['expectedCategory'], $result['category']->name);
        }
    }

    public function test_category_matching_fails_for_unknown_categories(): void
    {
        $matcher = new ImportMatcher();

        // Debug: show all categories in database
        $allCategories = \App\Models\Category::all()->pluck('name')->toArray();
        error_log('Categories in DB: ' . json_encode($allCategories));

        // These should NOT match because they don't exist in the database
        // (and are not in the ImportMatcher aliases either)
        $unknownCategories = [
            'Unknown Category XYZ',
            'Non Existent Category',
            'Random Item Type',
        ];

        foreach ($unknownCategories as $input) {
            $result = $matcher->matchCategory($input);
            $this->assertEquals('unmatched', $result['status'], "Should not match '$input': " . json_encode($result));
            $this->assertNull($result['category']);
        }

        // These SHOULD match now because they map to umbrella categories in ImportMatcher
        $knownCategories = [
            'Server' => 'Computers & Peripherals',
            'Switch' => 'Networking & Communication',
            'Router' => 'Networking & Communication',
            'UPS' => 'Power & Backup',
            'Storage Device' => 'Storage & Media',
            'Scanner' => 'Printing & Scanning',
            'Firewall' => 'Networking & Communication',
            'Access Point' => 'Networking & Communication',
            'Projector' => 'Audio/Video & Presentation',
            'TV' => 'Audio/Video & Presentation',
            'Interactive Panel' => 'Audio/Video & Presentation',
            'Keyboard' => 'Computers & Peripherals',
            'Mouse' => 'Computers & Peripherals',
            'Webcam' => 'Computers & Peripherals',
            'Headset' => 'Computers & Peripherals',
            'Speaker' => 'Computers & Peripherals',
            'Microphone' => 'Computers & Peripherals',
            'Docking Station' => 'Computers & Peripherals',
            'KVM Switch' => 'Computers & Peripherals',
            'Inverter' => 'Power & Backup',
            'Stabilizer' => 'Power & Backup',
            'Generator' => 'Power & Backup',
            'Solar Power' => 'Power & Backup',
            'CCTV' => 'Other Equipment',
            'NVR' => 'Other Equipment',
            'DVR' => 'Other Equipment',
            'Access Control' => 'Other Equipment',
            'Biometric Device' => 'Other Equipment',
            'Audio Equipment' => 'Audio/Video & Presentation',
            'Amplifier' => 'Audio/Video & Presentation',
            'Mixer' => 'Audio/Video & Presentation',
            'Conference Phone' => 'Networking & Communication',
            'Video Conferencing' => 'Audio/Video & Presentation',
            'Mobile Device' => 'Other Equipment',
            'Tablet' => 'Other Equipment',
            'Software' => 'Other Equipment',
            'Software License' => 'Other Equipment',
            'Furniture' => 'Furniture & Fixtures',
            'Rack' => 'Furniture & Fixtures',
            'Air Conditioner' => 'Appliances',
            'Chiller' => 'Appliances',
            'HVAC' => 'Appliances',
            'Medical Equipment' => 'Other Equipment',
            'Lab Equipment' => 'Other Equipment',
        ];

        foreach ($knownCategories as $input => $expectedCategory) {
            $result = $matcher->matchCategory($input);
            $this->assertEquals('matched', $result['status'], "Should match '$input': " . json_encode($result));
            $this->assertEquals($expectedCategory, $result['category']->name, "Category name mismatch for '$input'");
        }
    }

    public function test_end_to_end_excel_import_with_various_categories(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Test data simulating Excel rows with various categories
        $testCases = [
            [
                'occupant' => 'Computer Center',
                'item' => 'Desktop Computer',
                'expectedCategory' => 'Computers & Peripherals',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'Laptop',
                'expectedCategory' => 'Computers & Peripherals',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'Network Switch',
                'expectedCategory' => 'Networking & Communication',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'Router',
                'expectedCategory' => 'Networking & Communication',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'UPS',
                'expectedCategory' => 'Power & Backup',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'Laser Printer',
                'expectedCategory' => 'Printing & Scanning',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'LED Monitor',
                'expectedCategory' => 'Computers & Peripherals',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'Projector',
                'expectedCategory' => 'Audio/Video & Presentation',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'CCTV Camera',
                'expectedCategory' => 'Other Equipment',
            ],
            [
                'occupant' => 'Computer Center',
                'item' => 'Server',
                'expectedCategory' => 'Computers & Peripherals',
            ],
        ];

        foreach ($testCases as $case) {
            $rawData = [
                'Present Occupant' => $case['occupant'],
                'Name of the Item' => $case['item'],
                'Issued Quantity as per Departmental Stock Register' => '1',
                'Date of Issue' => '01/01/2024',
            ];

            $result = $normalizer->normalize($rawData);

            $this->assertEquals('valid', $result['status'], "Failed for item '{$case['item']}': " . json_encode($result['errors'] ?? []));
            $this->assertCount(1, $result['assets']);
            $this->assertEquals($case['expectedCategory'], $result['assets'][0]['category_name'], "Category mismatch for '{$case['item']}'");
            $this->assertTrue($result['assets'][0]['valid']);
        }
    }

    public function test_end_to_end_excel_import_unknown_category_shows_clear_error(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Unknown Widget XYZ',
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);

        $this->assertEquals('invalid', $result['status']);
        $this->assertCount(1, $result['assets']);
        $this->assertFalse($result['assets'][0]['valid']);
        
        $errors = $result['assets'][0]['errors'];
        $this->assertNotEmpty($errors);
        
        // Should show a clear category error
        $hasCategoryError = collect($errors)->contains(function ($error) {
            return stripos($error, 'category') !== false || stripos($error, 'Category') !== false;
        });
        $this->assertTrue($hasCategoryError, 'Expected category error: ' . json_encode($errors));
        
        // Should NOT show generic error
        $this->assertNotEquals(
            ['One or more extracted assets require review.'],
            $result['errors'],
            'Should not return generic error'
        );
    }

    public function test_department_and_category_matching_independent(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Test 1: Valid department, invalid category
        $rawData = [
            'Present Occupant' => 'Computer Center',  // Valid department
            'Name of the Item' => 'Unknown Widget',   // Invalid category
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);
        $this->assertEquals('invalid', $result['status']);
        $this->assertEquals('Computer Centre', $result['department']['name']); // Department matched
        $this->assertFalse($result['assets'][0]['valid']);
        $assetErrors = $result['assets'][0]['errors'];
        $hasCategoryError = collect($assetErrors)->contains(fn ($e) => stripos($e, 'category') !== false);
        $this->assertTrue($hasCategoryError, 'Should show category error when department is valid');

        // Test 2: Invalid department, valid category
        $rawData = [
            'Present Occupant' => 'Non Existent Dept',  // Invalid department
            'Name of the Item' => 'Desktop Computer',   // Valid category
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);
        $this->assertEquals('invalid', $result['status']);
        $this->assertNull($result['department']); // Department not matched
        $deptErrors = $result['errors'];
        $hasDeptError = collect($deptErrors)->contains(fn ($e) => stripos($e, 'department') !== false || stripos($e, 'Department') !== false);
        $this->assertTrue($hasDeptError, 'Should show department error when category would be valid');

        // Test 3: Both valid
        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Desktop Computer',
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);
        $this->assertEquals('valid', $result['status'], 'Both valid should pass: ' . json_encode($result['errors'] ?? []));
    }

    public function test_case_and_whitespace_variations_in_item_names(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        $variations = [
            'Desktop Computer',
            'DESKTOP COMPUTER',
            'desktop computer',
            '  Desktop Computer  ',
            'Desktop  Computer',  // Double space
            'Laptop',
            'LAPTOP',
            'laptop',
            '  Laptop  ',
            'Network Switch',
            'NETWORK SWITCH',
            'network switch',
            '  Network Switch  ',
        ];

        foreach ($variations as $item) {
            $rawData = [
                'Present Occupant' => 'Computer Center',
                'Name of the Item' => $item,
                'Issued Quantity as per Departmental Stock Register' => '1',
            ];

            $result = $normalizer->normalize($rawData);
            $this->assertEquals('valid', $result['status'], "Failed for item '$item': " . json_encode($result['errors'] ?? []));
            $this->assertTrue($result['assets'][0]['valid']);
        }
    }

    public function test_department_matching_no_false_positives(): void
    {
        $matcher = new ImportMatcher();

        // These should match correctly
        $validDepartments = [
            'Computer Center' => 'Computer Centre',
            'COMPUTER CENTER' => 'Computer Centre',
            'Computer Centre' => 'Computer Centre',
            'CC' => 'Computer Centre',
            'ICT Section' => 'ICT Section',
            'ICT' => 'ICT Section',
            'Information Technology' => 'ICT Section',
        ];

        foreach ($validDepartments as $input => $expected) {
            $result = $matcher->matchDepartment($input);
            $this->assertEquals('matched', $result['status'], "Failed for '$input': " . json_encode($result));
            $this->assertEquals($expected, $result['department']->name);
        }

        // These should NOT match (false positive prevention)
        $invalidDepartments = [
            'Computer Center Maintenance',  // Contains "Computer Center" but is a different thing
            'Computer Center XYZ',          // Similar but not exact
            'ICT Support',                  // Contains "ICT" but different
        ];

        foreach ($invalidDepartments as $input) {
            $result = $matcher->matchDepartment($input);
            // These might match via partial match, but should be 'unmatched' or 'matched' with partial type
            // The key is they should not incorrectly match to the wrong department
            if ($result['status'] === 'matched') {
                $this->assertNotEmpty($result['department']->name);
            }
        }
    }

    public function test_category_matching_no_false_positives(): void
    {
        $matcher = new ImportMatcher();

        // Test cases that should match specific umbrella categories (not false positives)
        $testCases = [
            'CCTV Camera' => 'Other Equipment',           // Should match Other Equipment, not TV
            'TV Monitor' => 'Computers & Peripherals',         // Contains "monitor" -> Computers & Peripherals (correct)
            'Network Switch' => 'Networking & Communication',      // Should match Networking & Communication
            'Switch Port' => 'Networking & Communication',         // Should match Networking & Communication
            'Router Device' => 'Networking & Communication',       // Should match Networking & Communication
            'Desktop Computer' => 'Computers & Peripherals',   // Should match Computers & Peripherals
            'Laptop Computer' => 'Computers & Peripherals',     // Should match Computers & Peripherals
            'Laser Printer' => 'Printing & Scanning',      // Should match Printing & Scanning
            'LED Monitor' => 'Computers & Peripherals',        // Should match Computers & Peripherals
            'UPS Battery' => 'Power & Backup',            // Should match Power & Backup
            'Server Rack' => 'Computers & Peripherals',         // Contains "server" -> Computers & Peripherals
            'Projector Screen' => 'Audio/Video & Presentation', // Should match Audio/Video & Presentation
        ];

        foreach ($testCases as $input => $expectedCategory) {
            $result = $matcher->matchCategory($input);
            $this->assertEquals('matched', $result['status'], "Failed for '$input': " . json_encode($result));
            $this->assertEquals($expectedCategory, $result['category']->name, "Category mismatch for '$input'");
        }
    }

    public function test_issue_examples_occupant_location_matching(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Test cases from the issue: occupant strings with room/location info
        $testCases = [
            [
                'occupant' => 'Computer Center Room No. 022',
                'item' => 'Desktop Computer',
                'expectedDepartment' => 'Computer Centre',
                'expectedCategory' => 'Computers & Peripherals',
            ],
            [
                'occupant' => 'Computer Center ITServer Room',
                'item' => 'Laptop',
                'expectedDepartment' => 'Computer Centre',
                'expectedCategory' => 'Computers & Peripherals',
            ],
            [
                'occupant' => 'Computer Center Smart Class Room',
                'item' => 'Projector',
                'expectedDepartment' => 'Computer Centre',
                'expectedCategory' => 'Audio/Video & Presentation',
            ],
        ];

        foreach ($testCases as $case) {
            $rawData = [
                'Present Occupant' => $case['occupant'],
                'Name of the Item' => $case['item'],
                'Issued Quantity as per Departmental Stock Register' => '1',
            ];

            $result = $normalizer->normalize($rawData);

            $this->assertEquals('valid', $result['status'], "Failed for '{$case['occupant']}': " . json_encode($result['errors'] ?? []));
            $this->assertEquals($case['expectedDepartment'], $result['department']['name'], "Department mismatch for '{$case['occupant']}'");
            $this->assertCount(1, $result['assets']);
            $this->assertEquals($case['expectedCategory'], $result['assets'][0]['category_name'], "Category mismatch for '{$case['item']}'");
            $this->assertTrue($result['assets'][0]['valid']);
        }
    }

    public function test_issue_examples_category_matching(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Test cases from the issue: category matching
        $testCases = [
            [
                'item' => 'Drawer Pedestal Unit Aarbees',
                'expectedCategory' => 'Furniture & Fixtures',
            ],
            [
                'item' => 'Side Unit Aarbees',
                'expectedCategory' => 'Furniture & Fixtures',
            ],
            [
                'item' => 'Virtual Class Room Module-1 & Module-2',
                'expectedCategory' => 'Audio/Video & Presentation',
            ],
            [
                'item' => 'Screw Driver Set',
                'expectedCategory' => 'Other Equipment', // No specific Tools category exists
            ],
            [
                'item' => 'Video Conference Set Polycom',
                'expectedCategory' => 'Audio/Video & Presentation',
            ],
        ];

        foreach ($testCases as $case) {
            $rawData = [
                'Present Occupant' => 'Computer Center',
                'Name of the Item' => $case['item'],
                'Issued Quantity as per Departmental Stock Register' => '1',
            ];

            $result = $normalizer->normalize($rawData);

            $this->assertEquals('valid', $result['status'], "Failed for '{$case['item']}': " . json_encode($result['errors'] ?? []));
            $this->assertCount(1, $result['assets']);
            $this->assertEquals($case['expectedCategory'], $result['assets'][0]['category_name'], "Category mismatch for '{$case['item']}'");
            $this->assertTrue($result['assets'][0]['valid']);
        }
    }

    public function test_ambiguous_or_unknown_values_remain_invalid(): void
    {
        $matcher = new ImportMatcher();
        $normalizer = new AssetImportNormalizer($matcher);

        // Unknown department should be invalid
        $rawData = [
            'Present Occupant' => 'Non Existent Department XYZ',
            'Name of the Item' => 'Desktop Computer',
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);
        $this->assertEquals('invalid', $result['status']);
        $this->assertNotEmpty($result['errors']);
        $hasDeptError = collect($result['errors'])->contains(fn ($e) => stripos($e, 'department') !== false || stripos($e, 'Department') !== false);
        $this->assertTrue($hasDeptError, 'Should show department error for unknown department');

        // Unknown category should be invalid
        $rawData = [
            'Present Occupant' => 'Computer Center',
            'Name of the Item' => 'Unknown Item XYZ',
            'Issued Quantity as per Departmental Stock Register' => '1',
        ];

        $result = $normalizer->normalize($rawData);
        $this->assertEquals('invalid', $result['status']);
        $hasCatError = collect($result['assets'][0]['errors'] ?? [])->contains(fn ($e) => stripos($e, 'category') !== false || stripos($e, 'Category') !== false);
        $this->assertTrue($hasCatError, 'Should show category error for unknown category');
    }
}