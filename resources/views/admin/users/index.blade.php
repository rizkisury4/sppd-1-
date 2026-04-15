<x-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Kelola Pengguna</h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm ring-1 ring-inset bg-blue-600 text-white hover:bg-blue-700 ring-blue-700/20 dark:bg-blue-500 dark:hover:bg-blue-400 dark:ring-white/10">Tambah User</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Role</th>
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Awal Bergabung</th>
                                <th class="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="px-3 py-2">{{ $u->name }}</td>
                                    <td class="px-3 py-2 capitalize">{{ $u->role }}</td>
                                    <td class="px-3 py-2">{{ $u->email }}</td>
                                    <td class="px-3 py-2">{{ $u->created_at?->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Edit</a>
                                        @if(auth()->id() !== $u->id)
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-block ml-2" onsubmit="return confirm('Yakin hapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-rose-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
