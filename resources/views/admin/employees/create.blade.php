<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Data Pegawai</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1">NIP</label>
                        <input name="nip" value="{{ old('nip') }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        @error('nip')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input name="name" value="{{ old('name') }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        @error('name')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Jabatan</label>
                        <input name="position" value="{{ old('position') }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        @error('position')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Status Pegawai</label>
                        <input name="employment_status" value="{{ old('employment_status') }}" class="w-full rounded border-gray-300 bg-white dark:bg-slate-800" required />
                        @error('employment_status')<div class="text-rose-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="active" value="1" {{ old('active', '1') ? 'checked' : '' }}> Aktif</label>
                    <div class="pt-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        <a href="{{ route('admin.employees.index') }}" class="ml-2 text-slate-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboard-layout>