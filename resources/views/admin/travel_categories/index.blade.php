<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Travel Categories</h2>
            <a href="{{ route('admin.travel-categories.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700">Tambah</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2">Kode</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Aktif</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $c)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="px-3 py-2">{{ $c->code }}</td>
                                <td class="px-3 py-2">{{ $c->name }}</td>
                                <td class="px-3 py-2">{{ $c->active ? 'Ya' : 'Tidak' }}</td>
                                <td class="px-3 py-2 flex gap-2">
                                    <a href="{{ route('admin.travel-categories.edit', $c) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.travel-categories.destroy', $c) }}" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
