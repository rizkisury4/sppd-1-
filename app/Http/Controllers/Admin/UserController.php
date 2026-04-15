<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $users = User::orderBy('name')->paginate(15, ['id','name','email','role','created_at']);
        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request): View
    {
        abort_if($request->user()->role !== 'admin', 403);
        $roles = ['admin','manager','finance','pegawai'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 403);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'role' => ['required','string', Rule::in(['admin','manager','finance','pegawai'])],
            'password' => ['required','string','min:6'],
        ]);
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);
        return redirect()->route('admin.users.index');
    }

    public function edit(Request $request, User $user): View
    {
        abort_if($request->user()->role !== 'admin', 403);
        $roles = ['admin','manager','finance','pegawai'];
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 403);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role' => ['required','string', Rule::in(['admin','manager','finance','pegawai'])],
            'password' => ['nullable','string','min:6'],
        ]);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
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
