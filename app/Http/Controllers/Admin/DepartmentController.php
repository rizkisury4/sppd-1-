<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(in_array(optional($request->user())->role, ['admin']), 403);
            return $next($request);
        });
    }

    public function index(): View
    {
        $items = Department::orderBy('name')->paginate(15);
        return view('admin.departments.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:departments,code'],
            'name' => ['required','string','max:255'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = $request->boolean('active', true);
        Department::create($data);
        return redirect()->route('admin.departments.index');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:50','unique:departments,code,'.$department->id],
            'name' => ['required','string','max:255'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = $request->boolean('active', true);
        $department->update($data);
        return redirect()->route('admin.departments.index');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();
        return redirect()->route('admin.departments.index');
    }
}

