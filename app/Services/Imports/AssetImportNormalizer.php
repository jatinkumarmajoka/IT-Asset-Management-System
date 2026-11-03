<?php

namespace App\Services\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;

class AssetImportNormalizer
{
    public function __construct(
        private ImportMatcher $matcher
    ) {}

    /**
     * Get the first available value from raw data using a list of possible keys.
     */
    private function getValue(array $raw, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $raw) && $raw[$key] !== null) {
                return $raw[$key];
            }
        }

        return null;
    }

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


        | IMPORTANT:


        | Do NOT split occupant by newline.


        | Example:


        | MoolChand


        | Auditorium


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


        | Location information


        |--------------------------------------------------------------------------

        */
        $locationText = $this->cleanOccupant(
            $this->getValue($raw, [
                'Location',
                'Location of Asset',
                'Physical Location',
                'Present Location',
                'Room',
                'Room No',
                'Room Number',
            ])
        );

        $presentRoomText = $this->cleanOccupant(
            $this->getValue($raw, [
                'Present Room Number',
                'Room Number',
                'Room No',
            ])
        );

        $presentLocationText = $this->cleanOccupant(
            $this->getValue($raw, [
                'Present Location',
                'Present Occupant Location',
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
            return $this->invalid([
                'No asset item name was found.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------


        | Department resolution


        |--------------------------------------------------------------------------


        */

        // Extract department name from occupant text (handles cases like "Computer Center Room No. 022")
        $occupantForDeptMatch = $this->extractDepartmentFromOccupant($occupantText);
        $departmentResult = $this->matcher->matchDepartment($occupantForDeptMatch);
        $department = $departmentResult['department'];

        if (! $department) {
            return [
                'status' => 'invalid',
                'errors' => [
                    "Department/Present Occupant '{$occupantText}' could not be matched. Original: {$departmentResult['original']}. Status: {$departmentResult['status']}. Match type: {$departmentResult['matchType']}",
                ],
                'assets' => [],
                'department' => null,
            ];
        }

        $itemCount = count($itemNames);

        // Normalize extracted auxiliary columns against the real item count.
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
            $errors[] = 'Make/Model count ('.count($makeModels).") does not match item count ({$itemCount}).";
        }

        /*
        |--------------------------------------------------------------------------


        | Issued quantity


        |--------------------------------------------------------------------------




        */

        if (
            count($issuedQuantities) !== $itemCount
            && count($issuedQuantities) !== 1
        ) {
            $errors[] = 'Issued quantity count ('.count($issuedQuantities).") does not match item count ({$itemCount}).";
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
            $errors[] = 'Physically verified quantity count ('.count($verifiedQuantities).") does not match item count ({$itemCount}).";
        }

        /*
        |--------------------------------------------------------------------------


        | Dates


        |--------------------------------------------------------------------------




        */

        if (
            ! empty($dates)
            && count($dates) !== 1
            && count($dates) !== $itemCount
        ) {
            $errors[] = 'Date count ('.count($dates).") does not match item count ({$itemCount}).";
        }

        /*
        |--------------------------------------------------------------------------


        | Barcode / serial


        |--------------------------------------------------------------------------


        */

        if (
            ! empty($barcodes)
            && count($barcodes) !== 1
            && count($barcodes) !== $itemCount
        ) {
            $errors[] = 'Barcode count ('.count($barcodes).") does not match item count ({$itemCount}).";
        }

        if (
            ! empty($serials)
            && count($serials) !== 1
            && count($serials) !== $itemCount
        ) {
            $errors[] = 'Serial number count ('.count($serials).") does not match item count ({$itemCount}).";
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

        $employee = $this->resolveEmployee($occupantText, $department);

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

            $makeModel = $this->valueAt($makeModels, $i, $itemCount);
            [$make, $model] = $this->splitMakeModel($makeModel);
            $issuedQuantity = $this->quantityAt($issuedQuantities, $i);
            $verifiedQuantity = $this->quantityAt($verifiedQuantities, $i);
            $date = $this->dateAt($dates, $i, $itemCount);
            $barcode = $this->valueAt($barcodes, $i, $itemCount);
            $serial = $this->valueAt($serials, $i, $itemCount);

            $categoryResult = $this->matcher->matchCategory($itemNames[$i]);
            $category = $categoryResult['category'];

            $assetErrors = [];

            /*
            |--------------------------------------------------------------------------


            | Quantity validation


            |--------------------------------------------------------------------------


            */

            if ($issuedQuantity === null) {
                $assetErrors[] = "Invalid issued quantity for '{$itemNames[$i]}'.";
            }

            /*
            |--------------------------------------------------------------------------


            | Date validation


            |--------------------------------------------------------------------------


            */

            $rawDateAtIndex = $this->rawValueAt($dates, $i, $itemCount);
            if (
                $rawDateAtIndex !== null
                && trim((string) $rawDateAtIndex) !== ''
                && $date === null
            ) {
                $assetErrors[] = "Invalid issue date for '{$itemNames[$i]}'.";
            }

            /*
            |--------------------------------------------------------------------------


            | Category


            |--------------------------------------------------------------------------


            */

            if (! $category) {
                $assetErrors[] = "Category could not be determined for '{$itemNames[$i]}'. Original: {$categoryResult['original']}. Status: {$categoryResult['status']}. Match type: {$categoryResult['matchType']}";
            }

            /*
            |--------------------------------------------------------------------------


            | Existing barcode duplicate


            |--------------------------------------------------------------------------


            */

            if (
                $barcode !== null
                && $barcode !== ''
                && Asset::where('barcode_number', $barcode)->exists()
            ) {
                $assetErrors[] = "Barcode '{$barcode}' already exists.";
            }

            /*
            |--------------------------------------------------------------------------


            | Existing serial duplicate


            |--------------------------------------------------------------------------


            */

            if (
                $serial !== null
                && $serial !== ''
                && Asset::where('asset_serial_number', $serial)->exists()
            ) {
                $assetErrors[] = "Serial number '{$serial}' already exists.";
            }

            /*
            |--------------------------------------------------------------------------


            | Location matching


            |--------------------------------------------------------------------------
            */

            // Use present_location if provided, otherwise use location text from Excel
            $locationText = $presentLocationText ?: $locationText;
            $locationResult = $this->matcher->matchLocation($locationText);
            $matchedLocation = $locationResult['location'];
            $customLocation = $locationResult['customLocation'];
            $locationMatchType = $locationResult['matchType'];

            // Extract room number
            $roomNumber = $this->matcher->extractRoomNumber($presentRoomText ?: $locationText);

            // If no room number from present_room, try from location text
            if (! $roomNumber) {
                $roomNumber = $this->matcher->extractRoomNumber($locationText);
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
                'item_name' => $itemNames[$i],
                'make' => $make,
                'model' => $model,
                'issued_quantity' => $issuedQuantity,
                'physically_verified_quantity' => $verifiedQuantity,
                'present_occupant_id' => $employee?->id,
                'present_occupant_name' => $employee?->name ?? $department->name,
                'asset_serial_number' => $serial ?: null,
                'barcode_number' => $barcode ?: null,
                'date_of_issue' => $date,
                'asset_status' => 'Active',
                'condition' => 'Good',
                'location' => null,
                'present_location' => $matchedLocation,
                'present_location_custom' => $customLocation,
                'present_room_number' => $roomNumber,
                'present_sub_department_id' => $department->import_sub_department_id ?? null,
                'remarks' => 'Imported from departmental stock register.',
                'errors' => $assetErrors,
                'valid' => empty($assetErrors),
                // Match metadata for preview
                'match_metadata' => [
                    'department' => [
                        'original' => $occupantText,
                        'matched' => $department->name,
                        'status' => $departmentResult['status'],
                        'match_type' => $departmentResult['matchType'],
                    ],
                    'category' => [
                        'original' => $categoryResult['original'],
                        'matched' => $category?->name,
                        'status' => $categoryResult['status'],
                        'match_type' => $categoryResult['matchType'],
                    ],
                    'location' => [
                        'original' => $locationResult['original'] ?? '',
                        'matched' => $matchedLocation,
                        'custom' => $customLocation,
                        'status' => $locationResult['status'],
                        'match_type' => $locationMatchType,
                    ],
                    'room' => [
                        'original' => $presentRoomText ?: $locationText,
                        'extracted' => $roomNumber,
                    ],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------


        | Overall status


        |--------------------------------------------------------------------------


        */

        $allValid = ! empty($assets) && collect($assets)->every(fn ($asset) => $asset['valid']);

        // Collect all field-level errors from individual assets
        $allErrors = [];
        foreach ($assets as $asset) {
            if (! empty($asset['errors'])) {
                foreach ($asset['errors'] as $error) {
                    if (! in_array($error, $allErrors, true)) {
                        $allErrors[] = $error;
                    }
                }
            }
        }

        return [
            'status' => $allValid ? 'valid' : 'invalid',
            'errors' => $allValid ? [] : $allErrors,
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'occupant' => [
                'employee_id' => $employee?->id,
                'name' => $employee?->name ?? $department->name,
            ],
            'assets' => $assets,
        ];
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

    private function cleanOccupant(mixed $value): string
    {
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

    /**
     * Extract department name from occupant text.
     * Handles cases like "Computer Center Room No. 022" -> "Computer Center"
     */
    private function extractDepartmentFromOccupant(string $occupantText): string
    {
        if (empty($occupantText)) {
            return $occupantText;
        }

        // Get all department aliases and names for matching
        $aliases = $this->matcher->getDepartmentAliases();
        $departments = Department::query()
            ->where('status', 'active')
            ->get()
            ->pluck('name')
            ->toArray();

        // Combine aliases keys and department names for searching
        $searchTerms = array_merge(array_keys($aliases), $departments);

        // Sort by length descending to match longer terms first
        usort($searchTerms, fn ($a, $b) => strlen($b) - strlen($a));

        $cleanOccupant = $this->matcher->normalizeForAliasMatch($occupantText);

        foreach ($searchTerms as $term) {
            $normalizedTerm = $this->matcher->normalizeForAliasMatch($term);
            if ($normalizedTerm !== '' && str_contains($cleanOccupant, $normalizedTerm)) {
                // Return the original term (alias key or department name) for matching
                return $term;
            }
        }

        return $occupantText;
    }

    private function resolveEmployee(
        string $occupant,
        Department $department
    ): ?Employee {
        if ($occupant === '') {
            return null;
        }

        $normalizedOccupant = $this->matcher->normalizeNameForComparison($occupant);

        return Employee::query()
            ->where('status', 'active')
            ->where('department_id', $department->id)
            ->get()
            ->first(fn ($employee) => $this->matcher->normalizeNameForComparison($employee->name) === $normalizedOccupant);
    }

    private function resolveCategory(string $itemName): ?Category
    {
        $categoryResult = $this->matcher->matchCategory($itemName);

        return $categoryResult['category'];
    }

    private function quantityAt(array $values, int $index): ?int
    {
        if (count($values) === 1) {
            $value = $values[0];
        } else {
            $value = $values[$index] ?? null;
        }

        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        $quantity = (int) $value;

        return $quantity >= 0 ? $quantity : null;
    }

    private function rawValueAt(array $values, int $index, int $itemCount): ?string
    {
        if (empty($values)) {
            return null;
        }
        if (count($values) === 1) {
            return (string) $values[0];
        }

        return array_key_exists($index, $values) ? (string) $values[$index] : null;
    }

    private function valueAt(array $values, int $index, int $itemCount): ?string
    {
        if (empty($values)) {
            return null;
        }
        if (count($values) === 1) {
            return trim($values[0]);
        }

        return isset($values[$index]) ? trim($values[$index]) : null;
    }

    private function dateAt(array $dates, int $index, int $itemCount): ?string
    {
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
            return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function splitMakeModel(?string $value): array
    {
        if (! $value) {
            return [null, null];
        }

        return [trim($value), null];
    }

    private function invalid(array $errors): array
    {
        return [
            'status' => 'invalid',
            'errors' => $errors,
            'assets' => [],
        ];
    }
}
