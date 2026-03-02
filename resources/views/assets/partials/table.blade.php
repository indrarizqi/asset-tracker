<div class="bg-white overflow-hidden sm:rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-[11px] font-extrabold uppercase tracking-wider">
                    <th class="px-4 py-4 w-10 text-center">No</th>
                    <th class="px-4 py-4 whitespace-nowrap">Asset ID</th>
                    <th class="px-4 py-4">Asset Name</th>
                    <th class="px-4 py-4">Person In Charge & Info</th>
                    <th class="px-4 py-4 text-center">Category</th>
                    <th class="px-4 py-4 text-center">Status</th>
                    <th class="px-4 py-4">Description</th>
                    <th class="px-4 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @if($assets->count() > 0)
                    @foreach($assets as $asset)
                    <tr class="hover:bg-blue-50/40 transition-colors duration-200 group">
                        
                        <td class="px-4 py-4 text-sm text-gray-400 font-medium text-center">
                            {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded-md text-sm group-hover:bg-white transition">
                                {{ $asset->asset_tag }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="font-bold text-gray-900 text-sm">{{ $asset->name }}</div>
                            <div class="text-[11px] text-gray-500 mt-1">
                                Kondisi: 
                                @php
                                    $kondisi = $asset->condition ?? 'Baik';
                                    $warnaKondisi = 'text-green-600';
                                    if (stripos($kondisi, 'rusak') !== false) $warnaKondisi = 'text-yellow-600';
                                    if (stripos($kondisi, 'total') !== false || stripos($kondisi, 'berat') !== false) $warnaKondisi = 'text-red-500 font-bold';
                                @endphp
                                <span class="font-medium {{ $warnaKondisi }}">{{ $kondisi }}</span>
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-blue-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $asset->person_in_charge ?? '-' }}
                            </div>
                            <div class="flex items-center text-[11px] text-gray-400 mt-1 font-medium">
                                <svg class="w-3.5 h-3.5 text-red-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Beli: {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : '-' }}
                            </div>
                            <div class="text-[11px] text-gray-500 mt-1">Lokasi: {{ $asset->location ?? '-' }}</div>
                            @if($asset->activeTransaction)
                                <div class="text-[11px] text-amber-600 mt-1 font-semibold">
                                    Dipinjam: {{ $asset->activeTransaction->borrower_name }}
                                    @if($asset->activeTransaction->due_at)
                                        (Jatuh tempo {{ \Carbon\Carbon::parse($asset->activeTransaction->due_at)->format('d M Y') }})
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-linear-to-r from-blue-500 to-purple-500 text-white shadow-sm">
                                {{ $asset->category }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            @php
                                $statusKey = strtolower(str_replace(' ', '_', $asset->status));
                                $colorClass = 'bg-gray-100 text-gray-700'; 
                                if (str_contains($statusKey, 'in_use')) { $colorClass = 'bg-green-100 text-green-700 border border-green-200'; } 
                                elseif (str_contains($statusKey, 'available')) { $colorClass = 'bg-blue-100 text-blue-700 border border-blue-200'; } 
                                elseif (str_contains($statusKey, 'maintenance')) { $colorClass = 'bg-yellow-100 text-yellow-700 border border-yellow-200'; } 
                                elseif (str_contains($statusKey, 'broken')) { $colorClass = 'bg-red-100 text-red-700 border border-red-200'; }
                            @endphp
                            <span class="inline-block px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-full {{ $colorClass }}">
                                {{ str_replace('_', ' ', $asset->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-600 truncate max-w-37.5" title="{{ $asset->description }}">
                            {{ $asset->description }}
                        </td>
                        
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">

                                <button type="button"
                                    @click="
                                        showActionModal = true; 
                                        actionData = {
                                            id: '{{ $asset->id }}',
                                            tag: '{{ $asset->asset_tag }}',
                                            name: '{{ addslashes($asset->name) }}',
                                            status: '{{ $asset->status }}'
                                        };
                                    "
                                    class="p-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm border border-emerald-100" 
                                    title="Update Status">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </button>

                                <button type="button"
                                    @click="showAssetModal = true; asset = {
                                        id: '{{ $asset->asset_tag }}',
                                        name: '{{ addslashes($asset->name) }}',
                                        pic: '{{ addslashes($asset->person_in_charge) }}',
                                        category: '{{ $asset->category }}',
                                        status: '{{ $asset->status }}',
                                        condition: '{{ $asset->condition ?? 'Baik' }}',
                                        price: 'Rp {{ number_format($asset->price ?? 0, 0, ',', '.') }}',
                                        purchase_date: '{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') : '-' }}',
                                        location: '{{ addslashes((string) $asset->location) }}',
                                        vendor: '{{ addslashes((string) $asset->vendor) }}',
                                        serial_number: '{{ addslashes((string) $asset->serial_number) }}',
                                        warranty_expiry_date: '{{ $asset->warranty_expiry_date ? \Carbon\Carbon::parse($asset->warranty_expiry_date)->format('d M Y') : '-' }}',
                                        description: '{{ addslashes(trim(preg_replace('/\s+/', ' ', $asset->description))) }}',
                                        edit_url: '{{ route('assets.edit', $asset->id) }}'
                                    }"
                                    class="p-2 text-blue-500 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm border border-blue-100" 
                                    title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline m-0 p-0 form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Asset"
                                        class="p-2 text-red-500 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm cursor-pointer border border-red-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            Data aset tidak ditemukan.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@if ($assets->hasPages())
    <div class="mt-2 px-1">
        {{ $assets->links('pagination.custom') }} 
        </div>
@endif