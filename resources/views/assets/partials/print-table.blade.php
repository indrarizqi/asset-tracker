<div class="bg-white overflow-hidden sm:rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4 text-center w-16">No</th>
                    <th class="px-6 py-4">Asset ID</th>
                    <th class="px-6 py-4">Asset Name</th>
                    <th class="px-6 py-4 text-center">Category</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4 text-center w-24">Options</th> 
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @if($assets->count() > 0)
                    @foreach($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                        <td class="px-6 py-4 text-center text-sm text-gray-400 font-medium">
                            {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-800 text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ $asset->asset_tag }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 text-sm">{{ $asset->name }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">Kondisi: {{ $asset->condition ?? 'Baik' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase">{{ $asset->category }}</span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @php
                                $statusKey = strtolower(str_replace(' ', '_', $asset->status));
                                $statusClass = 'bg-gray-100 text-gray-600'; 
                                if (str_contains($statusKey, 'in_use')) $statusClass = 'bg-green-50 text-green-600 border border-green-100';
                                elseif (str_contains($statusKey, 'maintenance')) $statusClass = 'bg-yellow-50 text-yellow-600 border border-yellow-100';
                                elseif (str_contains($statusKey, 'not_used')) $statusClass = 'bg-blue-50 text-blue-600 border border-blue-100';
                                elseif (str_contains($statusKey, 'broken')) $statusClass = 'bg-red-50 text-red-600 border border-red-100';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full uppercase {{ $statusClass }}">{{ str_replace('_', ' ', $asset->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">{{ $asset->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <input type="checkbox" value="{{ $asset->id }}" class="asset-checkbox w-5 h-5 rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 cursor-pointer transition-transform hover:scale-110">
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Data not found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@if ($assets->hasPages())
    <div class="mt-2 px-1">
        {{ $assets->links() }} 
        </div>
@endif