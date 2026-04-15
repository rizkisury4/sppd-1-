<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit User</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input name="name" value="{{ old('name', $user->name) }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        @error('name')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        @error('email')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Role</label>
                        <select name="role" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800">
                            @foreach($roles as $r)
                                <option value="{{ $r }}" @selected(old('role', $user->role)===$r)>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                        @error('role')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Password (kosongkan jika tidak diganti)</label>
                        <input type="password" name="password" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" />
                        @error('password')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="pt-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        <a href="{{ route('admin.users.index') }}" class="ml-2 text-slate-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>
