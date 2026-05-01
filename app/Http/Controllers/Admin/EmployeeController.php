<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(optional($request->user())->role === 'admin', 403);

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = Employee::query()->orderBy('name');
        $keyword = trim($request->string('q')->toString());

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('nip', 'like', "%{$keyword}%")
                    ->orWhere('position', 'like', "%{$keyword}%")
                    ->orWhere('employment_status', 'like', "%{$keyword}%");
            });
        }

        $employees = $query->paginate(15)->withQueryString();

        return view('admin.employees.index', compact('employees', 'keyword'));
    }

    public function create(): View
    {
        return view('admin.employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEmployee($request);
        $data['active'] = $request->boolean('active', true);

        Employee::create($data);

        return redirect()->route('admin.employees.index')->with('status', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $this->validateEmployee($request, $employee->id);
        $data['active'] = $request->boolean('active', true);

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('status', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('status', 'Data pegawai berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $data['file']->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->withErrors(['file' => 'File tidak dapat dibaca.']);
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

            return back()->withErrors(['file' => 'Header CSV tidak ditemukan.']);
        }

        $imported = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
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

            $imported++;
        }

        fclose($handle);

        return redirect()->route('admin.employees.index')->with('status', "Import selesai. {$imported} data pegawai diproses.");
    }

    protected function validateEmployee(Request $request, ?int $employeeId = null): array
    {
        return $request->validate([
            'nip' => ['required', 'string', 'max:50', 'unique:employees,nip'.($employeeId ? ','.$employeeId : '')],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'employment_status' => ['required', 'string', 'max:100'],
            'active' => ['nullable', 'boolean'],
        ]);
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
        $required = ['name', 'nip', 'position', 'employment_status'];

        foreach ($required as $column) {
            if (!in_array($column, $header, true)) {
                return false;
            }
        }

        return true;
    }
}