<?php

namespace App\Services\Imports;

use App\Models\Asset;

use App\Models\ImportBatch;

use App\Services\Imports\Parsers\DocxAssetParser;

use App\Services\Imports\Parsers\ExcelAssetParser;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Storage;

class AssetImportService

{

    public function __construct(

        private DocxAssetParser $docxParser,
        private ExcelAssetParser $excelParser,
        private AssetImportNormalizer $normalizer

    ) {}

    /**

     * Extract the uploaded document into import_rows.

     *

     * IMPORTANT:

     * This method ONLY extracts source data.

     *

     * It does NOT create assets.

     */

    public function process(ImportBatch $import): void

    {

        $import->update([

            'status' => 'processing',

        ]);

        try {

            DB::transaction(function () use ($import) {

                /*

                |--------------------------------------------------------------------------

                | Remove previously extracted rows

                |--------------------------------------------------------------------------

                |

                | This allows the same uploaded document to be processed again

                | without creating duplicate staging rows.

                |

                */

                $import->rows()->delete();

                $path = Storage::disk('local')

                    ->path($import->stored_path);

                /*

                |--------------------------------------------------------------------------

                | Parse document

                |--------------------------------------------------------------------------

                */

                $parsedDocument = match ($import->file_type) {

                    'docx' => $this->docxParser->parse($path),

                    'xlsx', 'csv', 'ods' => $this->excelParser->parse(
                        $path,
                        $import->file_type
                    ),

                    'xls' => throw new \RuntimeException(
                        'Legacy .xls files are not supported. Please save the file as .xlsx and upload again.'
                    ),

                    default => throw new \RuntimeException(
                        'Unsupported import file type: ' . $import->file_type
                    ),
                };

                /*

                |--------------------------------------------------------------------------

                | Normalize parser output

                |--------------------------------------------------------------------------

                |

                | Normally:

                |

                | [

                |     'headers' => [...],

                |     'rows' => [...]

                | ]

                |

                | We also support multiple document/table structures.

                |

                */

                $documents = $parsedDocument;

                if (

                    isset($documents['headers']) &&

                    isset($documents['rows'])

                ) {

                    $documents = [$documents];
                }

                $sourceRowNumber = 0;

                /*

                |--------------------------------------------------------------------------

                | Create staging rows

                |--------------------------------------------------------------------------

                */

                foreach ($documents as $document) {

                    $headers = $document['headers'] ?? [];

                    $rows = $document['rows'] ?? [];

                    if (

                        empty($headers) ||

                        empty($rows)

                    ) {

                        continue;
                    }

                    /*

                    |--------------------------------------------------------------------------

                    | Clean headers

                    |--------------------------------------------------------------------------

                    */

                    $headers = array_map(
    function ($header) {

        $header = (string) $header;

        // Replace line breaks and multiple spaces with one space.
        $header = preg_replace(
            '/\s+/',
            ' ',
            $header
        );

        return trim($header);
    },
    $headers
);
                    /*

                    |--------------------------------------------------------------------------

                    | Process source rows

                    |--------------------------------------------------------------------------

                    */

                    foreach ($rows as $row) {

                        $sourceRowNumber++;

                        $rowData = [];

                        foreach (

                            $headers as $index => $header

                        ) {

                            $rowData[$header] =

                                $row[$index] ?? '';
                        }

                        $import->rows()->create([

                            'row_number' =>

                            $sourceRowNumber,

                            'raw_data' =>

                            $rowData,

                            'normalized_data' =>

                            null,

                            'status' =>

                            'pending',

                            'errors' =>

                            null,

                        ]);
                    }
                }

                /*

                |--------------------------------------------------------------------------

                | Update import batch

                |--------------------------------------------------------------------------

                */

                $import->update([

                    'total_rows' =>

                    $sourceRowNumber,

                    'valid_rows' =>

                    0,

                    'invalid_rows' =>

                    0,

                    'duplicate_rows' =>

                    0,

                    'imported_rows' =>

                    0,

                    'status' =>

                    $sourceRowNumber > 0

                        ? 'parsed'

                        : 'failed',

                ]);
            });
        } catch (\Throwable $e) {

            $import->update([

                'status' => 'failed',

                'error_summary' => [

                    'message' => $e->getMessage(),

                ],

            ]);

            throw $e;
        }
    }

    /**

     * Normalize and validate extracted rows.

     *

     * IMPORTANT:

     * This method still does NOT create assets.

     *

     * It resolves:

     *

     * Department

     * Category

     * Employee

     * Dates

     * Quantities

     * Duplicates

     *

     * and stores the result in import_rows.normalized_data.

     */

    public function validate(ImportBatch $import): void

    {

        $import->load('rows');

        $validRows = 0;

        $invalidRows = 0;

        $duplicateRows = 0;

        DB::transaction(function () use (

            $import,

            &$validRows,

            &$invalidRows,

            &$duplicateRows

        ) {

            foreach ($import->rows as $row) {

                $result = $this->normalizer->normalize(

                    $row->raw_data ?? []

                );

                $rowStatus =

                    $result['status'] === 'valid'

                    ? 'valid'

                    : 'invalid';

                $row->update([

                    'normalized_data' =>

                    $result,

                    'status' =>

                    $rowStatus,

                    'errors' =>

                    $result['errors'] ?? null,

                ]);

                if ($result['status'] === 'valid') {

                    $validRows++;
                } else {

                    $invalidRows++;
                }

                /*

                |--------------------------------------------------------------------------

                | Duplicate tracking

                |--------------------------------------------------------------------------

                |

                | The normalizer can mark individual generated assets as

                | duplicate. Count the source row if any duplicate exists.

                |

                */

                $assets = $result['assets'] ?? [];

                $hasDuplicate = collect($assets)

                    ->contains(function ($asset) {

                        return !empty($asset['duplicate_of_asset_id'] ?? null);
                    });

                if ($hasDuplicate) {

                    $duplicateRows++;
                }
            }

            /*

            |--------------------------------------------------------------------------

            | Import status

            |--------------------------------------------------------------------------

            */

            $import->update([

                'valid_rows' =>

                $validRows,

                'invalid_rows' =>

                $invalidRows,

                'duplicate_rows' =>

                $duplicateRows,

                'status' =>

                $invalidRows === 0

                    ? 'validated'

                    : 'needs_review',

            ]);
        });
    }

    /**

     * Create one actual Asset record from an imported record.

     *

     * IMPORTANT:

     *

     * The normalizer has already resolved:

     *

     * department_id

     * category_id

     * present_occupant_id

     *

     * This method saves those foreign keys directly into assets.

     *

     * It intentionally does NOT store:

     *

     * department_name

     * category_name

     * present_occupant_name

     * errors

     * valid

     *

     * because those are import/staging values, not Asset columns.

     */

    public function createImportedAsset(array $data): Asset

    {

        /*

        |--------------------------------------------------------------------------

        | Resolve authenticated user

        |--------------------------------------------------------------------------

        */

        $userId = Auth::id();

        /*

        |--------------------------------------------------------------------------

        | Validate required foreign keys

        |--------------------------------------------------------------------------

        */

        if (empty($data['department_id'])) {

            throw new \RuntimeException(

                'Imported asset is missing department_id.'

            );
        }

        if (empty($data['category_id'])) {

            throw new \RuntimeException(

                'Imported asset is missing category_id.'

            );
        }

        /*

        |--------------------------------------------------------------------------

        | Build ONLY actual Asset fields

        |--------------------------------------------------------------------------

        */

        $assetData = [

            'asset_code' =>

            $data['asset_code'] ?? null,

            'department_id' =>

            $data['department_id'],

            'category_id' =>

            $data['category_id'],

            'item_name' =>

            $data['item_name'] ?? null,

            'make' =>

            $data['make'] ?? null,

            'model' =>

            $data['model'] ?? null,

            'issued_quantity' =>

            $data['issued_quantity'] ?? 0,

            'physically_verified_quantity' =>

            $data['physically_verified_quantity'] ?? 0,

            'present_occupant_id' =>

            $data['present_occupant_id'] ?? null,

            'asset_serial_number' =>

            $data['asset_serial_number'] ?? null,

            'barcode_number' =>

            $data['barcode_number'] ?? null,

            'date_of_issue' =>

            $data['date_of_issue'] ?? null,

            'asset_status' =>

            $data['asset_status'] ?? 'Active',

            'condition' =>

            $data['condition'] ?? 'Good',

            'location' =>

            $data['location'] ?? null,

            'remarks' =>

            $data['remarks']

                ?? 'Imported from departmental stock register.',

            'created_by' =>

            $userId,

            'updated_by' =>

            $userId,

        ];

        /*

        |--------------------------------------------------------------------------

        | Create Asset

        |--------------------------------------------------------------------------

        |

        | IMPORTANT:

        |

        | department_id and category_id are saved here.

        |

        */

        $asset = Asset::create($assetData);

        /*

        |--------------------------------------------------------------------------

        | Create Asset History

        |--------------------------------------------------------------------------

        */

        $asset->history()->create([

            'action' =>

            'imported',

            'new_value' =>

            json_encode(

                $asset->only([

                    'asset_code',

                    'item_name',

                    'department_id',

                    'category_id',

                    'barcode_number',

                    'asset_serial_number',

                    'present_occupant_id',

                    'issued_quantity',

                    'physically_verified_quantity',

                    'date_of_issue',

                ])

            ),

            'performed_by' =>

            $userId,

            'ip_address' =>

            Request::ip(),

            'created_at' =>

            now(),

        ]);

        return $asset;
    }

    /**

     * Import all valid normalized assets from a batch.

     *

     * This method is transactional.

     *

     * If one asset fails:

     *

     * ALL assets are rolled back.

     */

    public function confirmImport(

        ImportBatch $import

    ): int {

        if (!in_array(
            $import->status,
            ['validated', 'needs_review'],
            true
        )) {
            throw new \RuntimeException(
                'This import is not ready for confirmation. ' .
                    'Current status: ' . $import->status
            );
        }

        return DB::transaction(function () use ($import) {

            $import->load('rows');

            $imported = 0;

            foreach ($import->rows as $row) {

                $normalized =

                    $row->normalized_data;

                /*

                |--------------------------------------------------------------------------

                | Skip invalid rows

                |--------------------------------------------------------------------------

                */

                if (

                    !is_array($normalized) ||

                    ($normalized['status'] ?? null) !== 'valid'

                ) {

                    continue;
                }

                /*

                |--------------------------------------------------------------------------

                | Get normalized asset candidates

                |--------------------------------------------------------------------------

                */

                $assets =

                    $normalized['assets'] ?? [];

                foreach ($assets as $assetData) {

                    /*

                    |--------------------------------------------------------------------------

                    | Skip invalid generated assets

                    |--------------------------------------------------------------------------

                    */

                    if (

                        !($assetData['valid'] ?? false)

                    ) {

                        continue;
                    }

                    /*

                    |--------------------------------------------------------------------------

                    | Remove normalizer-only fields

                    |--------------------------------------------------------------------------

                    */

                    unset(

                        $assetData['department_name'],

                        $assetData['category_name'],

                        $assetData['present_occupant_name'],

                        $assetData['errors'],

                        $assetData['valid'],

                        $assetData['duplicate_of_asset_id']

                    );

                    /*

                    |--------------------------------------------------------------------------

                    | Generate internal asset code

                    |--------------------------------------------------------------------------

                    */

                    $assetData['asset_code'] =

                        $this->generateImportAssetCode(

                            $import->id,

                            $imported + 1

                        );

                    /*

                    |--------------------------------------------------------------------------

                    | Create actual Asset

                    |--------------------------------------------------------------------------

                    */

                    $this->createImportedAsset(

                        $assetData

                    );

                    $imported++;
                }

                /*

                |--------------------------------------------------------------------------

                | Mark staging row as imported

                |--------------------------------------------------------------------------

                */

                $row->update([

                    'status' => 'imported',

                ]);
            }

            /*

            |--------------------------------------------------------------------------

            | Update import batch

            |--------------------------------------------------------------------------

            */

            $import->update([

                'status' =>

                'imported',

                'imported_rows' =>

                $imported,

            ]);

            return $imported;
        });
    }

    /**

     * Generate a unique internal asset code for imported assets.

     *

     * Example:

     *

     * IMP-13-0001

     * IMP-13-0002

     * IMP-13-0003

     */

    private function generateImportAssetCode(

        int $batchId,

        int $sequence

    ): string {

        return sprintf(

            'IMP-%d-%04d',

            $batchId,

            $sequence

        );
    }
}
