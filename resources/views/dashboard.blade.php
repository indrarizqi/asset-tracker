<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Aset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <a href="{{ route('assets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        + Input Aset Baru
                    </a>
                    <a href="{{ route('assets.pdf') }}" target="_blank" class="ml-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                        🖨️ Cetak Semua QR
                    </a>
                </div>
                
                <div class="text-gray-600">
                    Login sebagai: <span class="font-bold uppercase text-blue-600">{{ Auth::user()->role }}</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">ID Tag</th>
                                <th class="border p-2 text-left">Nama Aset</th>
                                <th class="border p-2 text-left">Kategori</th>
                                <th class="border p-2 text-left">Status</th>
                                <th class="border p-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2 font-mono font-bold">{{ $asset->asset_tag }}</td>
                                <td class="border p-2">{{ $asset->name }}</td>
                                <td class="border p-2">
                                    <span class="px-2 py-1 rounded text-xs font-bold 
                                        {{ $asset->category == 'mobile' ? 'bg-purple-100 text-purple-800' : 
                                          ($asset->category == 'fixed' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800') }}">
                                        {{ ucfirst($asset->category) }}
                                    </span>
                                </td>
                                <td class="border p-2">
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-800">
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td class="border p-2 text-center">
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="text-blue-500 hover:underline text-sm mr-2">Edit</a>

                                    @if(Auth::user()->role === 'super_admin')
                                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus aset ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-sm">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="border p-4 text-center text-gray-500">Belum ada data aset.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>