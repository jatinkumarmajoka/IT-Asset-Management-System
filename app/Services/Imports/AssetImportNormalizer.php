<?php

namespace App\Services\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SubDepartment;
use Carbon\Carbon;

class AssetImportNormalizer
{
    public function normalize(array $raw): array
    {

        $itemNames = $this->splitLines(

            $this->getValue($raw, [

                'Name of the Item',

                'Item Name',

                'Name of Item',

                'Asset Name',

            ])

        );

        $makeModels = $this->splitLines(

            $this->getValue($raw, [

                'Make and Model of the Item',

                'Make and Model',

                'Make/Model',

                'Manufacturer & Model',

            ]),

            true
        );

        $issuedQuantities = $this->splitLines(

            $this->getValue($raw, [

                'Issued Quantity as per Departmental Stock Register',

                'Issued Quantity',

                'Stock Quantity',

                'Quantity Issued',

            ]),

            true
        );

        $verifiedQuantities = $this->splitLines(

            $this->getValue($raw, [

                'Quantity Physically Verified',

                'Physically Verified',

                'Verified Quantity',

                'Physical Qty',

            ]),

            true
        );

        /*



        |--------------------------------------------------------------------------



        | Present Occupant



        |--------------------------------------------------------------------------



        |



        | IMPORTANT:



        | Do NOT split occupant by newline.



        |



        | Example:



        | MoolChand



        | Auditorium



        |



        | becomes:



        | MoolChand Auditorium



        |



        */

        $occupantText = $this->cleanOccupant(

            $this->getValue($raw, [

                'Present Occupant',

                'present_occupant',

                'Occupant',

                'User',

                'Assigned To',

            ])

        );

        $dates = $this->splitLines(

            $this->getValue($raw, [

                'Date of Issue',

                'Issue Date',

                'Date Issued',

                'date_of_issue',

            ]),

            true
        );

        $barcodes = $this->splitLines(

            $this->getValue($raw, [

                'Barcode/Sticker Sr. No. of the Sticker pasted on the Asset',

                'Barcode/Sticker No.',

                'Barcode',

                'Sticker No.',

                'barcode_number',

            ]),

            true
        );

        $serials = $this->splitLines(

            $this->getValue($raw, [

                'Asset Serial Number',

                'Serial Number',

                'Serial',

                'asset_serial_number',

            ]),

            true
        );

        /*



        |--------------------------------------------------------------------------



        | Basic validation



        |--------------------------------------------------------------------------



        */

        if (empty($itemNames)) {

            return $this->invalid(

                ['No asset item name was found.']

            );

        }

        /*



        |--------------------------------------------------------------------------



        | Department resolution



        |--------------------------------------------------------------------------



        */

        $department = $this->resolveDepartment($occupantText);

        if (! $department) {

            return [

                'status' => 'invalid',

                'errors' => [

                    "Department/Present Occupant '{$occupantText}' could not be matched.",

                ],

                'assets' => [],

                'department' => null,

            ];

        }

        $itemCount = count($itemNames);

        // Normalize extracted auxiliary columns against the real item count.
        // Spreadsheet merged cells often create extra blank/duplicate lines.
        $makeModels = $this->alignSequentialValues($makeModels, $itemCount);
        $issuedQuantities = $this->alignSequentialValues($issuedQuantities, $itemCount);
        $verifiedQuantities = $this->alignSequentialValues($verifiedQuantities, $itemCount, true);
        $dates = $this->alignDateValues($dates, $itemCount);
        $barcodes = $this->alignSequentialValues($barcodes, $itemCount, true);
        $serials = $this->alignSequentialValues($serials, $itemCount, true);

        $errors = [];

        /*



        |--------------------------------------------------------------------------



        | Make / Model



        |--------------------------------------------------------------------------



        */

        if (

            ! empty($makeModels)

            && count($makeModels) !== $itemCount

            && count($makeModels) !== 1

        ) {

            $errors[] =

                'Make/Model count ('.count($makeModels).

                ") does not match item count ({$itemCount}).";

        }

        /*



        |--------------------------------------------------------------------------



        | Issued quantity



        |--------------------------------------------------------------------------



        |



        | Quantity MUST either:



        |



        | 1 value  -> apply to all



        | OR



        | item count values -> one per item



        |



        | Anything else is ambiguous.



        */

        if (

            count($issuedQuantities) !== $itemCount

            && count($issuedQuantities) !== 1

        ) {

            $errors[] =

                'Issued quantity count ('.count($issuedQuantities).

                ") does not match item count ({$itemCount}).";

        }

        /*



        |--------------------------------------------------------------------------



        | Physically verified quantity



        |--------------------------------------------------------------------------



        */

        if (
            ! empty($verifiedQuantities)
            && count($verifiedQuantities) !== $itemCount
            && count($verifiedQuantities) !== 1
        ) {

            $errors[] =

                'Physically verified quantity count ('.

                count($verifiedQuantities).

                ") does not match item count ({$itemCount}).";

        }

        /*



        |--------------------------------------------------------------------------



        | Dates



        |--------------------------------------------------------------------------



        |



        | Valid:



        |



        | 1 date       -> apply to all



        | itemCount    -> one date per item



        |



        | Anything else requires review.



        */

        if (

            ! empty($dates)

            && count($dates) !== 1

            && count($dates) !== $itemCount

        ) {

            $errors[] =

                'Date count ('.count($dates).

                ") does not match item count ({$itemCount}).";

        }

        /*



        |--------------------------------------------------------------------------



        | Barcode / serial



        |--------------------------------------------------------------------------



        |



        | Blank values are allowed.



        */

        if (

            ! empty($barcodes)

            && count($barcodes) !== 1

            && count($barcodes) !== $itemCount

        ) {

            $errors[] =

                'Barcode count ('.count($barcodes).

                ") does not match item count ({$itemCount}).";

        }

        if (

            ! empty($serials)

            && count($serials) !== 1

            && count($serials) !== $itemCount

        ) {

            $errors[] =

                'Serial number count ('.count($serials).

                ") does not match item count ({$itemCount}).";

        }

        /*



        |--------------------------------------------------------------------------



        | If source structure is ambiguous, stop here.



        |--------------------------------------------------------------------------



        */

        if (! empty($errors)) {

            return [

                'status' => 'invalid',

                'errors' => $errors,

                'department' => [

                    'id' => $department->id,

                    'name' => $department->name,

                ],

                'occupant' => [

                    'employee_id' => null,

                    'name' => $department->name,

                ],

                'assets' => [],

            ];

        }

        /*



        |--------------------------------------------------------------------------



        | Employee resolution



        |--------------------------------------------------------------------------



        */

        $employee = $this->resolveEmployee(

            $occupantText,

            $department

        );

        /*



        |--------------------------------------------------------------------------



        | Build individual asset candidates



        |--------------------------------------------------------------------------



        */

        $assets = [];

        for ($i = 0; $i < $itemCount; $i++) {

            $itemName = trim($itemNames[$i]);

            if ($itemName === '') {

                continue;

            }

            $makeModel = $this->valueAt(

                $makeModels,

                $i,

                $itemCount

            );

            [$make, $model] =

                $this->splitMakeModel($makeModel);

            $issuedQuantity =

                $this->quantityAt(

                    $issuedQuantities,

                    $i

                );

            $verifiedQuantity =

                $this->quantityAt(

                    $verifiedQuantities,

                    $i

                );

            $date =

                $this->dateAt(

                    $dates,

                    $i,

                    $itemCount

                );

            $barcode =

                $this->valueAt(

                    $barcodes,

                    $i,

                    $itemCount

                );

            $serial =

                $this->valueAt(

                    $serials,

                    $i,

                    $itemCount

                );

            $category =

                $this->resolveCategory($itemName);

            $assetErrors = [];

            /*



            |--------------------------------------------------------------------------



            | Quantity validation



            |--------------------------------------------------------------------------



            */

            if ($issuedQuantity === null) {

                $assetErrors[] =

                    "Invalid issued quantity for '{$itemName}'.";

            }

            /*



            |--------------------------------------------------------------------------



            | Date validation



            |--------------------------------------------------------------------------



            */

            $rawDateAtIndex = $this->rawValueAt(
                $dates,
                $i,
                $itemCount
            );

            if (
                $rawDateAtIndex !== null
                && trim((string) $rawDateAtIndex) !== ''
                && $date === null
            ) {
                $assetErrors[] =
                    "Invalid issue date for '{$itemName}'.";
            }

            /*



            |--------------------------------------------------------------------------



            | Category



            |--------------------------------------------------------------------------



            */

            if (! $category) {

                $assetErrors[] =

                    "Category could not be determined for '{$itemName}'.";

            }

            /*



            |--------------------------------------------------------------------------



            | Existing barcode duplicate



            |--------------------------------------------------------------------------



            */

            if (

                $barcode !== null

                && $barcode !== ''

                && Asset::where(

                    'barcode_number',

                    $barcode

                )->exists()

            ) {

                $assetErrors[] =

                    "Barcode '{$barcode}' already exists.";

            }

            /*



            |--------------------------------------------------------------------------



            | Existing serial duplicate



            |--------------------------------------------------------------------------



            */

            if (

                $serial !== null

                && $serial !== ''

                && Asset::where(

                    'asset_serial_number',

                    $serial

                )->exists()

            ) {

                $assetErrors[] =

                    "Serial number '{$serial}' already exists.";

            }

            /*



            |--------------------------------------------------------------------------



            | Candidate



            |--------------------------------------------------------------------------



            */

            $assets[] = [

                'asset_code' => null,

                'department_id' => $department->id,

                'department_name' => $department->name,

                'category_id' => $category?->id,

                'category_name' => $category?->name,

                'item_name' => $itemName,

                'make' => $make,

                'model' => $model,

                'issued_quantity' => $issuedQuantity,

                'physically_verified_quantity' => $verifiedQuantity,

                /*



                | Employee is optional.



                | NULL means department itself is occupant.



                */

                'present_occupant_id' => $employee?->id,

                'present_occupant_name' => $employee?->name

                    ?? $department->name,

                'asset_serial_number' => $serial ?: null,

                'barcode_number' => $barcode ?: null,

                'date_of_issue' => $date,

                'asset_status' => 'Active',

                'condition' => 'Good',

                'location' => null,

                'present_room_number' => $department->import_room_number ?? null,

                'present_sub_department_id' => $department->import_sub_department_id ?? null,

                'remarks' => 'Imported from departmental stock register.',

                'errors' => $assetErrors,

                'valid' => empty($assetErrors),

            ];

        }

        /*



        |--------------------------------------------------------------------------



        | Overall status



        |--------------------------------------------------------------------------



        */

        $allValid =

            ! empty($assets)

            && collect($assets)->every(

                fn ($asset) => $asset['valid']

            );

        return [

            'status' => $allValid

                    ? 'valid'

                    : 'invalid',

            'errors' => $allValid

                    ? []

                    : [

                        'One or more extracted assets require review.',

                    ],

            'department' => [

                'id' => $department->id,

                'name' => $department->name,

            ],

            'occupant' => [

                'employee_id' => $employee?->id,

                'name' => $employee?->name

                    ?? $department->name,

            ],

            'assets' => $assets,

        ];

    }

    /*



    |--------------------------------------------------------------------------



    | Helpers



    |--------------------------------------------------------------------------



    */

    private function getValue(

        array $raw,

        array $keys

    ): mixed {

        foreach ($keys as $key) {

            if (

                array_key_exists($key, $raw)

                && $raw[$key] !== null

            ) {

                return $raw[$key];

            }

        }

        return null;

    }

    /**
     * Pack extracted values in source order and align them to the number of
     * actual asset items. Blank extraction artefacts are ignored.
     */
    private function alignSequentialValues(
        array $values,
        int $itemCount,
        bool $optional = false
    ): array {
        if ($itemCount <= 0) {
            return [];
        }

        $nonEmpty = array_values(array_filter(
            $values,
            fn ($value) => trim((string) $value) !== ''
        ));

        if (empty($nonEmpty)) {
            return $optional ? [] : array_fill(0, $itemCount, '');
        }

        if (count($nonEmpty) === 1) {
            return [$nonEmpty[0]];
        }

        // Never let duplicated merged-cell values create extra asset positions.
        $nonEmpty = array_slice($nonEmpty, 0, $itemCount);

        return array_pad($nonEmpty, $itemCount, '');
    }

    /**
     * Preserve relative blank positions for dates. For example, a date on the
     * first source line and another date on the last source line maps to the
     * first and last actual asset respectively.
     */
    private function alignDateValues(array $values, int $itemCount): array
    {
        if ($itemCount <= 0 || empty($values)) {
            return [];
        }

        $nonEmpty = [];
        $lastSourceIndex = count($values) - 1;

        foreach ($values as $sourceIndex => $value) {
            if (trim((string) $value) !== '') {
                $nonEmpty[] = [
                    'source_index' => $sourceIndex,
                    'value' => trim((string) $value),
                ];
            }
        }

        if (empty($nonEmpty)) {
            return [];
        }

        if (count($nonEmpty) === 1) {
            return [$nonEmpty[0]['value']];
        }

        $aligned = array_fill(0, $itemCount, '');
        $maxItemIndex = $itemCount - 1;

        foreach ($nonEmpty as $entry) {
            $target = $lastSourceIndex > 0
                ? (int) round(($entry['source_index'] / $lastSourceIndex) * $maxItemIndex)
                : 0;

            $target = max(0, min($target, $maxItemIndex));

            if ($aligned[$target] !== '') {
                $placed = false;
                for ($distance = 1; $distance <= $maxItemIndex; $distance++) {
                    foreach ([$target - $distance, $target + $distance] as $candidate) {
                        if ($candidate >= 0 && $candidate < $itemCount && $aligned[$candidate] === '') {
                            $target = $candidate;
                            $placed = true;
                            break 2;
                        }
                    }
                }
                if (! $placed) {
                    continue;
                }
            }

            $aligned[$target] = $entry['value'];
        }

        return $aligned;
    }

    private function splitLines(
        mixed $value,
        bool $preserveEmpty = false
    ): array {
        if ($value === null) {
            return [];
        }

        $value = str_replace(
            ["\r\n", "\r"],
            "\n",
            (string) $value
        );

        $lines = array_map(
            function ($line) {
                $line = preg_replace('/\s+/', ' ', (string) $line);

                return trim($line);
            },
            explode("\n", $value)
        );

        if (! array_filter($lines, fn ($line) => $line !== '')) {
            return [];
        }

        if (! $preserveEmpty) {
            return array_values(array_filter($lines, fn ($line) => $line !== ''));
        }

        return $lines;
    }

    private function cleanOccupant(

        mixed $value

    ): string {

        if ($value === null) {

            return '';

        }

        return trim(

            preg_replace(

                '/\s+/',

                ' ',

                str_replace(

                    ["\r\n", "\r", "\n"],

                    ' ',

                    (string) $value

                )

            )

        );

    }

    private function resolveEmployee(

        string $occupant,

        Department $department

    ): ?Employee {

        if ($occupant === '') {

            return null;

        }

        return Employee::query()

            ->where('status', 'active')

            ->where(

                'department_id',

                $department->id

            )

            ->get()

            ->first(

                fn ($employee) => $this->normalizeName(

                    $employee->name

                ) ===

                    $this->normalizeName(

                        $occupant

                    )

            );

    }

    private function resolveCategory(

        string $itemName

    ): ?Category {

        $cleanItem = $this->normalizeName($itemName);

        if ($cleanItem === '') {

            return null;

        }

        /*

        |--------------------------------------------------------------------------

        | 1. Exact existing category match

        |--------------------------------------------------------------------------

        */

        $category = Category::query()

            ->where('status', 'active')

            ->get()

            ->first(

                fn ($category) => $this->normalizeName($category->name) === $cleanItem

            );

        if ($category) {

            return $category;

        }

        /*

        |--------------------------------------------------------------------------

        | 2. Map common item variants to an existing category.

        |

        | If the category exists, use it.

        | If it does not exist, create the canonical category below.

        |--------------------------------------------------------------------------

        */

        $categoryMap = [

            'cctv' => [

                'canonical' => 'CCTV',

                'keywords' => [

                    'cctv',

                    'bullet camera',

                    'dome camera',

                ],

            ],

            'switch' => [

                'canonical' => 'Switch',

                'keywords' => [

                    'switch',

                    'poe switch',

                ],

            ],

            'ups' => [

                'canonical' => 'UPS',

                'keywords' => [

                    'ups',

                ],

            ],

            'printer' => [

                'canonical' => 'Printer',

                'keywords' => [

                    'printer',

                ],

            ],

            'desktop' => [

                'canonical' => 'Desktop',

                'keywords' => [

                    'desktop',

                ],

            ],

            'server' => [

                'canonical' => 'Server',

                'keywords' => [

                    'server',

                ],

            ],

            'storage' => [

                'canonical' => 'Storage Device',

                'keywords' => [

                    'internal hdd',

                    'external hdd',

                    'hdd',

                ],

            ],

            'air_conditioner' => [

                'canonical' => 'Air Conditioner',

                'keywords' => [

                    'air conditioner',

                ],

            ],

            'nvr' => [

                'canonical' => 'NVR',

                'keywords' => [

                    'nvr',

                ],

            ],

            'lcd_tv' => [

                'canonical' => 'LCD TV',

                'keywords' => [

                    'lcd tv',

                ],

            ],

            'rack' => [

                'canonical' => 'Rack',

                'keywords' => [

                    'rack',

                ],

            ],

            'software' => [

                'canonical' => 'Software',

                'keywords' => [

                    'software',

                ],

            ],

        ];

        foreach ($categoryMap as $definition) {

            foreach ($definition['keywords'] as $keyword) {

                $normalizedKeyword = $this->normalizeName($keyword);

                if (

                    $normalizedKeyword !== ''

                    && str_contains($cleanItem, $normalizedKeyword)

                ) {

                    $canonicalName = $definition['canonical'];

                    /*

                    | First try the canonical category if it already exists.

                    */

                    $category = Category::query()

                        ->where('status', 'active')

                        ->get()

                        ->first(

                            fn ($category) => $this->normalizeName($category->name) ===

                                $this->normalizeName($canonicalName)

                        );

                    if ($category) {

                        return $category;

                    }

                    /*

                    | Create the missing canonical category automatically.

                    | This is the same behavior as automatic department

                    | creation: no manual setup is required.

                    */

                    return Category::create([

                        'name' => $canonicalName,

                        'status' => 'active',

                    ]);

                }

            }

        }

        /*

        |--------------------------------------------------------------------------

        | 3. Unknown item type

        |--------------------------------------------------------------------------

        |

        | Do not reject the row just because the category is not pre-seeded.

        | Create a category from the actual source item name.

        |--------------------------------------------------------------------------

        */

        return Category::create([

            'name' => trim($itemName),

            'status' => 'active',

        ]);

    }

    private function quantityAt(

        array $values,

        int $index

    ): ?int {

        if (count($values) === 1) {

            $value = $values[0];

        } else {

            $value = $values[$index] ?? null;

        }

        if (

            $value === null

            || $value === ''

            || ! is_numeric($value)

        ) {

            return null;

        }

        $quantity = (int) $value;

        return $quantity >= 0

            ? $quantity

            : null;

    }

    private function rawValueAt(
        array $values,
        int $index,
        int $itemCount
    ): ?string {
        if (empty($values)) {
            return null;
        }
        if (count($values) === 1) {
            return (string) $values[0];
        }

        return array_key_exists($index, $values)
            ? (string) $values[$index]
            : null;
    }

    private function valueAt(

        array $values,

        int $index,

        int $itemCount

    ): ?string {

        if (empty($values)) {

            return null;

        }

        if (count($values) === 1) {

            return trim($values[0]);

        }

        return isset($values[$index])

            ? trim($values[$index])

            : null;

    }

    private function dateAt(

        array $dates,

        int $index,

        int $itemCount

    ): ?string {

        if (empty($dates)) {

            return null;

        }

        if (count($dates) === 1) {

            $value = $dates[0];

        } else {

            $value = $dates[$index] ?? null;

        }

        if (! $value) {

            return null;

        }

        try {

            return Carbon::createFromFormat(

                'd/m/Y',

                trim($value)

            )->format('Y-m-d');

        } catch (\Throwable) {

            return null;

        }

    }

    private function splitMakeModel(

        ?string $value

    ): array {

        if (! $value) {

            return [null, null];

        }

        /*



        | The source column is explicitly



        | "Make and Model".



        |



        | Because values such as:



        | "SOPHOS Firewall"



        | "Numeric Digital 1000 series"



        |



        | cannot safely be separated automatically,



        | preserve the complete source value as Make.



        */

        return [

            trim($value),

            null,

        ];

    }

    private function normalizeName(

        string $value

    ): string {

        $value = str_replace(

            ["\r\n", "\r", "\n"],

            ' ',

            $value

        );

        $value =

            preg_replace(

                '/\s+/',

                ' ',

                $value

            );

        return mb_strtolower(

            trim($value)

        );

    }

    private function invalid(

        array $errors

    ): array {

        return [

            'status' => 'invalid',

            'errors' => $errors,

            'assets' => [],

        ];

    }

    private function resolveDepartment(
        string $occupant
    ): ?Department {
        $cleanOccupant = $this->cleanOccupant($occupant);

        if ($cleanOccupant === '') {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Extract room number from occupant text
        |--------------------------------------------------------------------------
        | Examples:
        | "ICT Section 313" -> department: "ICT Section", room: "313"
        | "Lab 101" -> department: "Lab", room: "101", is_lab: true
        | "Computer Lab 205" -> department: "Computer Lab", room: "205", is_lab: true
        | "Physics Laboratory 12" -> department: "Physics Laboratory", room: "12", is_lab: true
        |--------------------------------------------------------------------------
        */

        $roomNumber = null;
        $isLab = false;

        // Pattern to match room numbers at the end (e.g., "Section 313", "Lab 101", "Room 205")
        // Also handles cases like "ICT-313", "ICT 313", "Section-313"
        if (preg_match('/^(.+?)\s+(?:room|rm|no|#)?\s*(\d{2,4})\s*$/i', $cleanOccupant, $matches)) {
            $departmentName = trim($matches[1]);
            $roomNumber = $matches[2];
        } elseif (preg_match('/^(.+?)[\s\-]+(\d{2,4})\s*$/i', $cleanOccupant, $matches)) {
            // Fallback: any text followed by separator and 2-4 digits at end
            $departmentName = trim($matches[1]);
            $roomNumber = $matches[2];
        } else {
            $departmentName = $cleanOccupant;
        }

        // Check if it's a lab
        if (preg_match('/\b(lab|laboratory)\b/i', $departmentName)) {
            $isLab = true;
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize department/section name
        |--------------------------------------------------------------------------
        |
        | 7Hostel (Boys & Girls)
        | becomes:
        | Hostel (Boys & Girls)
        |
        | 5G SOET Lab remains unchanged.
        |--------------------------------------------------------------------------
        */

        $departmentName = $this->normalizeDepartmentName(
            $departmentName
        );

        /*
        |--------------------------------------------------------------------------
        | Find existing active department
        |--------------------------------------------------------------------------
        */

        $department = Department::query()
            ->where('status', 'active')
            ->get()
            ->first(
                fn ($department) => $this->normalizeName($department->name) ===
                    $this->normalizeName($departmentName)
            );

        if ($department) {
            // Store room number and lab flag on department for later use
            $department->import_room_number = $roomNumber;
            $department->import_is_lab = $isLab;

            // If it's a lab, try to find/create a "Lab" sub-department
            $importSubDepartmentId = null;
            if ($isLab) {
                $subDept = SubDepartment::query()
                    ->where('department_id', $department->id)
                    ->where('status', 'active')
                    ->get()
                    ->first(fn ($sd) => $this->normalizeName($sd->name) === 'lab');

                if (! $subDept) {
                    $subDept = SubDepartment::create([
                        'department_id' => $department->id,
                        'name' => 'Lab',
                        'code' => 'LAB',
                        'status' => 'active',
                        'description' => 'Automatically created for lab imports.',
                    ]);
                }
                $importSubDepartmentId = $subDept->id;
            }

            // Store sub-department ID for later use
            $department->import_sub_department_id = $importSubDepartmentId;

            return $department;
        }

        /*
        |--------------------------------------------------------------------------
        | Missing department -> create automatically
        |--------------------------------------------------------------------------
        |
        | We create ONLY departments.
        | We never create fake employees.
        |--------------------------------------------------------------------------
        */

        $newDepartment = Department::create([
            'name' => $departmentName,
            'code' => $this->generateDepartmentCode(
                $departmentName
            ),
            'description' => 'Automatically created during asset import.',
            'status' => 'active',
        ]);

        // Store room number and lab flag on department for later use
        $newDepartment->import_room_number = $roomNumber;
        $newDepartment->import_is_lab = $isLab;

        return $newDepartment;
    }

    private function normalizeDepartmentName(

        string $name

    ): string {

        $name = $this->cleanOccupant($name);

        if ($name === '') {

            return '';

        }

        /*

        |--------------------------------------------------------------------------

        | Handle the known PDF/DOCX extraction artifact only.

        |

        | 7Hostel (Boys & Girls) -> Hostel (Boys & Girls)

        |

        | IMPORTANT:

        | Do NOT generically remove leading digits.

        | Values such as 5G SOET Lab are legitimate department names.

        |--------------------------------------------------------------------------

        */

        if (preg_match('/^\d+Hostel\s*\(Boys\s*&\s*Girls\)$/i', $name)) {

            return 'Hostel (Boys & Girls)';

        }

        return $name;

    }

    private function generateDepartmentCode(

        string $name

    ): string {

        $base = strtoupper(

            preg_replace(

                '/[^A-Za-z0-9]+/',

                '-',

                trim($name)

            )

        );

        $base = trim($base, '-');

        if ($base === '') {

            $base = 'DEPT';

        }

        $code = $base;

        $counter = 1;

        while (

            Department::query()

                ->where('code', $code)

                ->exists()

        ) {

            $counter++;

            $code = $base.'-'.$counter;

        }

        return $code;

    }
}
