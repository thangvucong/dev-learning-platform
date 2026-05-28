<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class CourseClassStudentImportService
{
    /**
     * Read an uploaded Excel/CSV file and extract student identifiers.
     *
     * Implemented without external composer dependencies:
     * - `.csv`: parse with SplFileObject
     * - `.xlsx`: parse with ZipArchive + XML (sharedStrings + sheet1.xml)
     */
    public function extractMemberTokens(UploadedFile $file): array
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());

        if ($ext === 'csv') {
            return $this->extractFromCsv($file);
        }

        if ($ext === 'xlsx') {
            return $this->extractFromXlsx($file);
        }

        throw new RuntimeException('Định dạng file không được hỗ trợ. Vui lòng dùng `.xlsx` hoặc `.csv`.');
    }

    private function extractFromCsv(UploadedFile $file): array
    {
        $tokens = [];
        $path = $file->getPathname();
        if (!is_readable($path)) {
            return $tokens;
        }

        $fh = new \SplFileObject($path);
        $fh->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $rowIndex = 0;
        $colToUse = 0; // default: first column
        $headerDetected = false;

        foreach ($fh as $row) {
            if (!is_array($row) || $row === []) {
                continue;
            }

            $rowIndex++;
            if ($rowIndex === 1) {
                $normalizedHeader = [];
                foreach ($row as $colIndex => $cell) {
                    $normalizedHeader[(int) $colIndex] = mb_strtolower(trim((string) $cell));
                }

                foreach ($normalizedHeader as $colIndex => $name) {
                    if ($name === 'email') {
                        $colToUse = (int) $colIndex;
                        $headerDetected = true;
                    }
                    if ($name === 'user_id' || $name === 'userid' || $name === 'user id' || $name === 'id') {
                        $colToUse = (int) $colIndex;
                        $headerDetected = true;
                    }
                }

                // If no recognized header, treat row 1 as data (use default column A).
                if ($headerDetected === false) {
                    $raw = isset($row[$colToUse]) ? trim((string) $row[$colToUse]) : '';
                    if ($raw !== '') {
                        $tokens[] = $raw;
                    }
                }

                continue;
            }

            $raw = isset($row[$colToUse]) ? trim((string) $row[$colToUse]) : '';
            if ($raw === '') {
                continue;
            }

            $tokens[] = $raw;
        }

        // If no header was detected, SplFileObject already uses first row as header.
        // That means we effectively dropped one row; accept this tradeoff for simplicity.
        if ($headerDetected === false) {
            // Caller can re-upload if format differs.
        }

        return $tokens;
    }

    private function extractFromXlsx(UploadedFile $file): array
    {
        $tokens = [];
        $path = $file->getPathname();

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return $tokens;
        }

        $sharedStrings = [];
        $sharedStringsPath = 'xl/sharedStrings.xml';
        if ($zip->locateName($sharedStringsPath) !== false) {
            $sharedXml = $zip->getFromName($sharedStringsPath);
            if (is_string($sharedXml) && $sharedXml !== '') {
                $sharedDoc = @simplexml_load_string($sharedXml);
                if ($sharedDoc && isset($sharedDoc->si)) {
                    foreach ($sharedDoc->si as $si) {
                        $texts = [];
                        foreach ($si->xpath('.//t') as $tNode) {
                            $texts[] = (string) $tNode;
                        }
                        $sharedStrings[] = implode('', $texts);
                    }
                }
            }
        }

        $sheetPath = 'xl/worksheets/sheet1.xml';
        $sheetXml = $zip->getFromName($sheetPath);
        if (!is_string($sheetXml) || $sheetXml === '') {
            return $tokens;
        }

        $sheetDoc = @simplexml_load_string($sheetXml);
        if (!$sheetDoc || !isset($sheetDoc->sheetData->row)) {
            return $tokens;
        }

        /** @var array<int, array<string, string>> $rows */
        $rows = [];

        foreach ($sheetDoc->sheetData->row as $rowEl) {
            $rowIndex = (int) $rowEl['r'];
            $rows[$rowIndex] = $rows[$rowIndex] ?? [];

            if (!isset($rowEl->c)) {
                continue;
            }

            foreach ($rowEl->c as $cellEl) {
                $cellRef = (string) $cellEl['r']; // e.g. A1
                if ($cellRef === '') {
                    continue;
                }

                if (!preg_match('/^([A-Z]+)(\d+)$/i', $cellRef, $m)) {
                    continue;
                }

                $col = strtoupper((string) $m[1]);
                $type = (string) $cellEl['t'];

                $value = '';
                if ($type === 's') {
                    $idx = isset($cellEl->v) ? (int) $cellEl->v : -1;
                    $value = $idx >= 0 && isset($sharedStrings[$idx]) ? (string) $sharedStrings[$idx] : '';
                } elseif ($type === 'inlineStr' && isset($cellEl->is->t)) {
                    $value = (string) $cellEl->is->t;
                } else {
                    $value = isset($cellEl->v) ? (string) $cellEl->v : '';
                }

                $value = trim($value);
                if ($value === '') {
                    continue;
                }

                $rows[$rowIndex][$col] = $value;
            }
        }

        if ($rows === []) {
            return [];
        }

        $firstRowIndex = min(array_keys($rows));
        $headerRow = $rows[$firstRowIndex] ?? [];

        $emailCol = null;
        $userIdCol = null;
        foreach ($headerRow as $col => $name) {
            $name = mb_strtolower(trim((string) $name));
            if ($name === 'email') {
                $emailCol = $col;
            }
            if ($name === 'user_id' || $name === 'userid' || $name === 'user id' || $name === 'id') {
                $userIdCol = $col;
            }
        }

        // prioritize user_id if both exist; otherwise email; fallback column A
        $colToUse = $userIdCol ?? $emailCol ?? 'A';
        $startRow = ($emailCol !== null || $userIdCol !== null) ? ($firstRowIndex + 1) : $firstRowIndex;

        foreach ($rows as $rowIndex => $row) {
            if ((int) $rowIndex < $startRow) {
                continue;
            }

            if (!isset($row[$colToUse])) {
                continue;
            }

            $raw = trim((string) $row[$colToUse]);
            if ($raw === '') {
                continue;
            }

            $tokens[] = $raw;
        }

        return $tokens;
    }



}

