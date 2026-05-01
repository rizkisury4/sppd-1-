<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()->role !== 'admin', 403);
        $users = User::with('employee')->orderBy('name')->paginate(15, ['id','name','email','role','employee_id','created_at']);
        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request): View
    {
        abort_if($request->user()->role !== 'admin', 403);
        $roles = ['admin','manager','direksi','finance','pegawai'];
        $employees = Employee::where('active', true)
            ->whereNotIn('id', User::whereNotNull('employee_id')->pluck('employee_id'))
            ->orderBy('name')
            ->get(['id','name','nip']);
        return view('admin.users.create', compact('roles', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 403);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'role' => ['required','string', Rule::in(['admin','manager','direksi','finance','pegawai'])],
            'employee_id' => ['nullable','integer','exists:employees,id','unique:users,employee_id'],
            'password' => ['required','string','min:6'],
        ]);
        if ($data['role'] === 'pegawai' && empty($data['employee_id'])) {
            return back()->withErrors(['employee_id' => 'Akun pegawai harus terhubung ke data karyawan.'])->withInput();
        }
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'employee_id' => $data['role'] === 'pegawai' ? $data['employee_id'] : null,
            'password' => Hash::make($data['password']),
        ]);
        return redirect()->route('admin.users.index');
    }

    public function edit(Request $request, User $user): View
    {
        abort_if($request->user()->role !== 'admin', 403);
        $roles = ['admin','manager','direksi','finance','pegawai'];
        $employees = Employee::where('active', true)
            ->where(function ($query) use ($user) {
                $query->whereNotIn('id', User::whereNotNull('employee_id')->where('id', '!=', $user->id)->pluck('employee_id'));
                if ($user->employee_id) {
                    $query->orWhere('id', $user->employee_id);
                }
            })
            ->orderBy('name')
            ->get(['id','name','nip']);
        return view('admin.users.edit', compact('user','roles','employees'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 403);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role' => ['required','string', Rule::in(['admin','manager','direksi','finance','pegawai'])],
            'employee_id' => ['nullable','integer','exists:employees,id', Rule::unique('users','employee_id')->ignore($user->id)],
            'password' => ['nullable','string','min:6'],
        ]);
        if ($data['role'] === 'pegawai' && empty($data['employee_id'])) {
            return back()->withErrors(['employee_id' => 'Akun pegawai harus terhubung ke data karyawan.'])->withInput();
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->employee_id = $data['role'] === 'pegawai' ? $data['employee_id'] : null;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        return redirect()->route('admin.users.index');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 403);
        if ($request->user()->id === $user->id) {
            return redirect()->route('admin.users.index');
        }
        $user->delete();
        return redirect()->route('admin.users.index');
    }
}
