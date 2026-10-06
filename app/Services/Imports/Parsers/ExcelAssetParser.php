<?php

namespace App\Services\Imports\Parsers;

use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\ODS\Reader as OdsReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use RuntimeException;

class ExcelAssetParser
{
    /**
     * Extract spreadsheet data only.
     *
     * No departments, categories, employees, or assets are created here.
     * The extracted rows are later passed to AssetImportNormalizer.
     */
    public function parse(string $path, string $fileType = 'xlsx'): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('Spreadsheet file not found: ' . $path);
        }

        $type = strtolower($fileType ?: pathinfo($path, PATHINFO_EXTENSION));

        return match ($type) {
            'xlsx' => $this->read(new XlsxReader(), $path),
            'csv'  => $this->read(new CsvReader(), $path),
            'ods'  => $this->read(new OdsReader(), $path),
            'xls'  => throw new RuntimeException(
                'Legacy .xls files are not supported by OpenSpout. Save the workbook as .xlsx and upload it again.'
            ),
            default => throw new RuntimeException(
                'Unsupported spreadsheet format: .' . $type
            ),
        };
    }

    private function read(object $reader, string $path): array
    {
        $reader->open($path);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $rows = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $rows[] = $this->values($row);
                }

                if (!$rows) {
                    continue;
                }

                $headerIndex = $this->findHeaderRow($rows);

                if ($headerIndex === null) {
                    continue;
                }

                $headers = $this->cleanHeaders($rows[$headerIndex]);
                $dataRows = [];

                for ($i = $headerIndex + 1, $count = count($rows); $i < $count; $i++) {
                    $values = $rows[$i];

                    if ($this->emptyRow($values)) {
                        continue;
                    }

                    $values = $this->fit($values, count($headers));

                    // Ignore repeated header rows inside the sheet.
                    if ($this->isRepeatedHeader($values)) {
                        continue;
                    }

                    $dataRows[] = $values;
                }

                if ($headers && $dataRows) {
                    return [
                        'headers' => $headers,
                        'rows' => $dataRows,
                    ];
                }
            }
        } finally {
            $reader->close();
        }

        throw new RuntimeException(
            'No usable spreadsheet table found. Expected columns such as Name of the Item, Present Occupant and Date of Issue.'
        );
    }

    private function values(object $row): array
    {
        $values = [];

        foreach ($row->getCells() as $cell) {
            $value = $cell->getValue();

            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('d/m/Y');
            } elseif (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif ($value === null) {
                $value = '';
            }

            $values[] = trim((string) $value);
        }

        return $values;
    }

    /**
     * Detects the real header row instead of assuming row 1.
     * This matches the supplied departmental workbook whose table begins
     * around row 6.
     */
    private function findHeaderRow(array $rows): ?int
    {
        $signals = [
            'name of the item',
            'issued quantity',
            'quantity physically verified',
            'present occupant',
            'date of issue',
            'barcode',
        ];

        foreach ($rows as $index => $row) {
            $joined = $this->normalize(implode(' | ', $row));
            $matches = 0;

            foreach ($signals as $signal) {
                if (str_contains($joined, $this->normalize($signal))) {
                    $matches++;
                }
            }

            if ($matches >= 3) {
                return $index;
            }
        }

        return null;
    }

    private function cleanHeaders(array $headers): array
    {
        $result = [];

        foreach ($headers as $index => $header) {
            $header = preg_replace('/\s+/u', ' ', trim((string) $header));
            $result[] = $header !== ''
                ? $header
                : 'Column ' . ($index + 1);
        }

        return $result;
    }

    private function fit(array $values, int $count): array
    {
        if (count($values) < $count) {
            return array_pad($values, $count, '');
        }

        return count($values) > $count
            ? array_slice($values, 0, $count)
            : $values;
    }

    private function emptyRow(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function isRepeatedHeader(array $values): bool
    {
        $text = strtolower(implode(' | ', $values));

        return str_contains($text, 'name of the item')
            && str_contains($text, 'present occupant');
    }

    private function normalize(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^\pL\pN]+/u', ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', $value));
    }
}
