<?php

namespace App\Services\Imports\Parsers;

use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;

class DocxAssetParser
{
    /**
     * Parse tables from a DOCX file.
     */
    public function parse(string $filePath): array
    {
        if (! is_file($filePath)) {
            throw new RuntimeException(
                'DOCX file not found: '.$filePath
            );
        }

        try {
            $phpWord = IOFactory::load($filePath);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Unable to read the DOCX file: '
                .$e->getMessage()
            );
        }

        $tables = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                if (! is_object($element)) {
                    continue;
                }

                $this->findTables(
                    $element,
                    $tables
                );
            }
        }

        if (empty($tables)) {
            throw new RuntimeException(
                'No tables were found in this DOCX file.'
            );
        }

        return $tables;
    }

    /**
     * Recursively find Word tables.
     */
    private function findTables(
        object $element,
        array &$tables
    ): void {
        if ($element instanceof Table) {
            $tables[] = $this->parseTable($element);

            return;
        }

        if (! method_exists($element, 'getElements')) {
            return;
        }

        foreach ($element->getElements() as $child) {

            if (! is_object($child)) {
                continue;
            }

            $this->findTables(
                $child,
                $tables
            );
        }
    }

    /**
     * Convert one Word table into headers and rows.
     */
    private function parseTable(
        Table $table
    ): array {
        $rows = [];

        foreach ($table->getRows() as $row) {

            $cells = [];

            foreach ($row->getCells() as $cell) {

                $cells[] = $this->extractCellText(
                    $cell
                );
            }

            $rows[] = $cells;
        }

        if (empty($rows)) {
            return [
                'headers' => [],
                'rows' => [],
            ];
        }

        $headerIndex = $this->detectHeaderRow(
            $rows
        );

        if ($headerIndex === null) {
            return [
                'headers' => [],
                'rows' => $rows,
            ];
        }

        $headers = $this->cleanHeaders(
            $rows[$headerIndex]
        );

        $dataRows = array_slice(
            $rows,
            $headerIndex + 1
        );

        return [
            'headers' => $headers,
            'rows' => $dataRows,
        ];
    }

    /**
     * Find the first useful header row.
     */
    private function detectHeaderRow(
        array $rows
    ): ?int {
        foreach ($rows as $index => $row) {

            $nonEmpty = 0;

            foreach ($row as $cell) {

                if (trim((string) $cell) !== '') {
                    $nonEmpty++;
                }
            }

            if ($nonEmpty >= 2) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Extract text from a table cell.
     *
     * IMPORTANT:
     * We preserve line breaks because your stock register
     * contains multiple assets inside one table row.
     */
    private function extractCellText(
        object $cell
    ): string {
        $parts = [];

        if (! method_exists($cell, 'getElements')) {
            return '';
        }

        foreach ($cell->getElements() as $element) {

            if (! is_object($element)) {
                continue;
            }

            $text = $this->extractElementText(
                $element
            );

            if ($text !== '') {
                $parts[] = $text;
            }
        }

        return $this->cleanText(
            implode("\n", $parts)
        );
    }

    /**
     * Recursively extract text while preserving
     * separate Word elements as separate lines.
     */
    private function extractElementText(
        object $element
    ): string {
        if (method_exists($element, 'getText')) {

            $text = $element->getText();

            if (is_string($text)) {
                return $this->cleanText($text);
            }
        }

        if (! method_exists($element, 'getElements')) {
            return '';
        }

        $parts = [];

        foreach ($element->getElements() as $child) {

            if (! is_object($child)) {
                continue;
            }

            $text = $this->extractElementText(
                $child
            );

            if ($text !== '') {
                $parts[] = $text;
            }
        }

        return $this->cleanText(
            implode("\n", $parts)
        );
    }

    /**
     * Preserve line breaks but normalize spaces/tabs.
     */
    private function cleanText(
        string $value
    ): string {
        $lines = preg_split(
            "/\r\n|\r|\n/",
            $value
        );

        $lines = array_map(
            function ($line) {

                return trim(
                    preg_replace(
                        '/[ \t]+/u',
                        ' ',
                        $line
                    )
                );
            },
            $lines
        );

        $lines = array_filter(
            $lines,
            fn ($line) => $line !== ''
        );

        return implode(
            "\n",
            $lines
        );
    }

    /**
     * Clean detected column names.
     */
    private function cleanHeaders(
        array $headers
    ): array {
        return array_map(
            function ($header) {

                return $this->cleanText(
                    (string) $header
                );
            },
            $headers
        );
    }

    /**
     * Convert a multiline cell into individual values.
     */
    public function splitCellValues(
        ?string $value
    ): array {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $values = preg_split(
            "/\r\n|\r|\n/",
            $value
        );

        return array_values(
            array_filter(
                array_map(
                    fn ($item) => trim($item),
                    $values
                ),
                fn ($item) => $item !== ''
            )
        );
    }
}
