<?php

namespace App\Services;

class SimpleXlsxReader
{
    /**
     * Parse an XLSX file into a 2D array of strings.
     *
     * @throws \Exception
     */
    public static function parse(string $filePath): array
    {
        $zip = new \ZipArchive;
        if ($zip->open($filePath) !== true) {
            throw new \Exception('Gagal membuka file Excel.');
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $sharedStringsEntry = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsEntry) {
            // Remove namespaces for easier SimpleXML parsing
            $cleanXml = preg_replace('/xmlns="[^"]+"/', '', $sharedStringsEntry);
            $xml = @simplexml_load_string($cleanXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $parts = [];
                        foreach ($si->r as $r) {
                            if (isset($r->t)) {
                                $parts[] = (string) $r->t;
                            }
                        }
                        $sharedStrings[] = implode('', $parts);
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read sheet1.xml
        $sheetEntry = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (! $sheetEntry) {
            $zip->close();
            throw new \Exception('Format worksheet Excel tidak valid (sheet1.xml tidak ditemukan).');
        }

        $cleanSheetXml = preg_replace('/xmlns="[^"]+"/', '', $sheetEntry);
        $xmlSheet = @simplexml_load_string($cleanSheetXml);
        $zip->close();

        if (! $xmlSheet) {
            throw new \Exception('Gagal mengurai XML worksheet Excel.');
        }

        $rows = [];
        if (isset($xmlSheet->sheetData->row)) {
            foreach ($xmlSheet->sheetData->row as $rowNode) {
                $rowIndex = (int) $rowNode['r'];
                $rowCells = [];

                if (isset($rowNode->c)) {
                    foreach ($rowNode->c as $cell) {
                        $cellRef = (string) $cell['r']; // e.g. "A1", "B1"
                        preg_match('/^[A-Z]+/', $cellRef, $colMatches);
                        $colLetter = $colMatches[0] ?? '';

                        if ($colLetter === '') {
                            continue;
                        }

                        $value = '';
                        $type = (string) $cell['t'];

                        if ($type === 's' && isset($cell->v)) {
                            // Shared string
                            $idx = (int) $cell->v;
                            $value = $sharedStrings[$idx] ?? '';
                        } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                            $value = (string) $cell->is->t;
                        } elseif ($type === 'inlineStr' && isset($cell->is->r)) {
                            $parts = [];
                            foreach ($cell->is->r as $r) {
                                if (isset($r->t)) {
                                    $parts[] = (string) $r->t;
                                }
                            }
                            $value = implode('', $parts);
                        } elseif ($type === 'b' && isset($cell->v)) {
                            $value = ((string) $cell->v === '1') ? 'true' : 'false';
                        } elseif (isset($cell->v)) {
                            $value = (string) $cell->v;
                        }

                        $rowCells[$colLetter] = $value;
                    }
                }

                if (! empty($rowCells)) {
                    // Map column letters to 0-based indices
                    $rowArray = [];
                    foreach ($rowCells as $colLetter => $val) {
                        $colIndex = self::colLetterToIndex($colLetter);
                        $rowArray[$colIndex] = $val;
                    }

                    // Fill in any column gaps in the row array
                    $maxIndex = count($rowArray) > 0 ? max(array_keys($rowArray)) : -1;
                    for ($i = 0; $i <= $maxIndex; $i++) {
                        if (! isset($rowArray[$i])) {
                            $rowArray[$i] = '';
                        }
                    }
                    ksort($rowArray);
                    $rows[$rowIndex] = $rowArray;
                }
            }
        }

        // Sort rows by row index
        ksort($rows);

        // Fill row gaps if any, and convert to 0-indexed array of rows
        $normalizedRows = [];
        if (! empty($rows)) {
            $maxRowIndex = max(array_keys($rows));
            $minRowIndex = min(array_keys($rows));

            // Usually sheet rows start from 1
            for ($r = $minRowIndex; $r <= $maxRowIndex; $r++) {
                $normalizedRows[] = $rows[$r] ?? [];
            }
        }

        return $normalizedRows;
    }

    private static function colLetterToIndex(string $col): int
    {
        $index = 0;
        $len = strlen($col);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($col[$i]) - 64);
        }

        return $index - 1;
    }
}
