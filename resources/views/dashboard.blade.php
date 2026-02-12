<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 flex flex-col sm:flex-row items-start gap-4">

                <a href="{{ route('assets.create') }}"
                    class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Asset
                </a>

                <a href="{{ route('report.assets') }}" target="_blank"
                    class="inline-flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500 focus:ring-offset-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Download PDF Report
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-[11px] font-extrabold uppercase tracking-wider">
                                <th class="px-6 py-4 rounded-tl-xl w-10 text-center">No</th>
                                <th class="px-6 py-4">Asset ID</th>
                                <th class="px-6 py-4">Asset Name</th>
                                <th class="px-6 py-4">Person In Charge & Info</th>
                                <th class="px-6 py-4 text-center">Asset Category</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Condition</th>
                                <th class="px-6 py-4 rounded-tr-xl text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($assets as $asset)
                            <tr
                                class="hover:bg-blue-50/40 transition-colors duration-200 group border-b border-gray-50 last:border-none">

                                <td class="px-6 py-4 text-sm text-gray-400 font-medium text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded-md text-sm group-hover:bg-white transition">{{ $asset->asset_tag }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-[11px] text-gray-500 mt-1">
                                        Kondisi: <span class="font-medium 
                                    {{ strtolower($asset->condition) == 'baik' ? 'text-green-600' : '' }}
                                    {{ strtolower($asset->condition) == 'rusak ringan' ? 'text-yellow-500' : '' }}
                                    {{ strtolower($asset->condition) == 'rusak berat' ? 'text-red-500' : '' }}
                                ">
                                            {{ $asset->condition ?? 'Baik' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm font-semibold text-gray-700">
                                        <svg class="w-4 h-4 text-blue-500 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        {{ $asset->person_in_charge ?? 'Tidak ada' }}
                                    </div>
                                    <div class="flex items-center text-[11px] text-gray-400 mt-1 font-medium">
                                        <svg class="w-3.5 h-3.5 text-red-400 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Beli:
                                        {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-block px-3 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full bg-linear-to-r from-blue-500 to-purple-500 text-white shadow-sm">
                                        {{ $asset->category }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php
                                    $statusKey = strtolower(str_replace(' ', '_', $asset->status));
                                    $colorClass = 'bg-gray-100 text-gray-700';

                                    if (str_contains($statusKey, 'available')) {
                                    $colorClass = 'bg-green-100 text-green-700';
                                    } elseif (str_contains($statusKey, 'use')) {
                                    $colorClass = 'bg-blue-100 text-blue-700';
                                    } elseif (str_contains($statusKey, 'maintenance')) {
                                    $colorClass = 'bg-yellow-100 text-yellow-700';
                                    }
                                    @endphp

                                    <span
                                        class="inline-block px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider rounded-full {{ $colorClass }}">
                                        {{ str_replace('_', ' ', $asset->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $asset->description }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">

                                        <a href="{{ route('assets.edit', $asset->id) }}" title="Edit Aset"
                                            class="p-2 text-blue-500 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                </path>
                                            </svg>
                                        </a>

                                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST"
                                            class="inline m-0 p-0"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Aset"
                                                class="p-2 text-red-500 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>