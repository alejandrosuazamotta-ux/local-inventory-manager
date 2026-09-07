<?php

namespace Database\Seeders\Concerns;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait ParsesExcelData
{
    /**
     * Load all data rows (header excluded) from a seeder xlsx file.
     */
    protected function loadRows(string $filename): array
    {
        $path = database_path('seeders/data/' . $filename);
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        array_shift($rows);

        return array_values($rows);
    }

    /**
     * Parse a currency cell like "$1,234" or "1,234" into a float.
     */
    protected function money(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace(['$', ' ', ','], '', (string) $value);
    }

    /**
     * Trim a cell and treat blank/"N/A" values as absent.
     */
    protected function text(mixed $value, ?string $default = null): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || strtoupper($value) === 'N/A') {
            return $default;
        }

        return $value;
    }

    /**
     * Parse a date/datetime cell using the given format, or null when absent.
     */
    protected function parseDate(mixed $value, string $format = 'd/m/Y'): ?Carbon
    {
        $value = $this->text($value);

        if ($value === null) {
            return null;
        }

        return Carbon::createFromFormat($format, $value);
    }

    /**
     * Extract the trailing integer from a document code like "FAC-174" or "CRE-11".
     */
    protected function docNumber(mixed $value): ?int
    {
        if (preg_match('/(\d+)/', (string) $value, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
