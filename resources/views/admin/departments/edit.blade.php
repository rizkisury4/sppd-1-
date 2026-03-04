<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Department</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block mb-1">Kode</label>
                        <input name="code" class="w-full rounded border-gray-300" value="{{ $department->code }}" required />
                    </div>
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input name="name" class="w-full rounded border-gray-300" value="{{ $department->name }}" required />
                    </div>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="active" {{ $department->active ? 'checked' : '' }}> Aktif</label>
                    <div>
                        <button class="px-4 py-2 rounded bg-blue-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
