<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeCsvSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('pak, dicek dulu ya.csv');

        if (!is_file($path)) {
            return;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            return;
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = $this->detectDelimiter((string) $firstLine);
        $mappedHeader = null;

        while (($header = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!is_array($header)) {
                continue;
            }

            $header = $this->trimEdgeEmptyColumns($header);

            if ($header === []) {
                continue;
            }

            $candidateHeader = array_map([$this, 'normalizeHeader'], $header);

            if ($this->isSupportedHeader($candidateHeader)) {
                $mappedHeader = $candidateHeader;
                break;
            }
        }

        if (!is_array($mappedHeader)) {
            fclose($handle);

            return;
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!is_array($row)) {
                continue;
            }

            $row = $this->trimEdgeEmptyColumns($row);

            if ($this->rowIsEmpty($row)) {
                continue;
            }

            $row = array_pad($row, count($mappedHeader), null);
            $payload = array_combine($mappedHeader, array_slice($row, 0, count($mappedHeader)));

            if (!is_array($payload)) {
                continue;
            }

            $record = [
                'nip' => trim((string) ($payload['nip'] ?? '')),
                'name' => trim((string) ($payload['name'] ?? '')),
                'position' => trim((string) ($payload['position'] ?? '')),
                'employment_status' => trim((string) ($payload['employment_status'] ?? '')),
                'active' => true,
            ];

            if ($record['nip'] === '' || $record['name'] === '' || $record['position'] === '' || $record['employment_status'] === '') {
                continue;
            }

            Employee::updateOrCreate(
                ['nip' => $record['nip']],
                [
                    'name' => $record['name'],
                    'position' => $record['position'],
                    'employment_status' => $record['employment_status'],
                    'active' => true,
                ]
            );
        }

        fclose($handle);
    }

    protected function normalizeHeader(?string $header): string
    {
        $normalized = Str::of((string) $header)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        return match ($normalized) {
            'nama' => 'name',
            'nip' => 'nip',
            'jabatan' => 'position',
            'status_pegawai', 'statuspegawai' => 'employment_status',
            default => $normalized,
        };
    }

    protected function detectDelimiter(string $line): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $bestCount = -1;

        foreach ($delimiters as $delimiter) {
            $count = substr_count($line, $delimiter);

            if ($count > $bestCount) {
                $bestDelimiter = $delimiter;
                $bestCount = $count;
            }
        }

        return $bestDelimiter;
    }

    protected function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function trimEdgeEmptyColumns(array $row): array
    {
        while ($row !== [] && trim((string) reset($row)) === '') {
            array_shift($row);
        }

        while ($row !== [] && trim((string) end($row)) === '') {
            array_pop($row);
        }

        return array_values($row);
    }

    protected function isSupportedHeader(array $header): bool
    {
        foreach (['name', 'nip', 'position', 'employment_status'] as $column) {
            if (!in_array($column, $header, true)) {
                return false;
            }
        }

        return true;
    }
}