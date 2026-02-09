<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asset Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <a href="{{ route('assets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        + Add New Asset
                    </a>
                    <a href="{{ route('assets.pdf') }}" target="_blank" class="ml-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                        🖨️ Print All QR Code
                    </a>
                    @if(Auth::user()->role === 'super_admin')
                        <a href="{{ route('report.assets') }}" target="_blank" class="ml-2 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow flex items-center gap-2">
                            📄 Download Laporan PDF
                        </a>
                    @endif
                </div>
                
                <div class="text-gray-600">
                    Login as : <span class="font-bold uppercase text-blue-600">{{ Auth::user()->role }}</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">Asset ID</th>
                                <th class="border p-2 text-left">Asset Name</th>
                                <th class="border p-2 text-left">Person In Charge & Info</th>
                                <th class="border p-2 text-left">Asset Category</th>
                                <th class="border p-2 text-left">Status</th>
                                <th class="border p-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2 font-mono font-bold">{{ $asset->asset_tag }}</td>
                                <td class="border p-2">
                                    <div class="font-bold">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-500">Kondisi: {{ $asset->asset_condition ?? '-' }}</div>
                                </td>
                                
                                <td class="border p-2">
                                    @if($asset->person_in_charge)
                                        <div class="text-sm font-semibold text-gray-800">👤 {{ $asset->person_in_charge }}</div>
                                    @else
                                        <div class="text-xs text-gray-400 italic">-</div>
                                    @endif
                                    
                                    <div class="text-xs text-gray-500 mt-1">
                                        📅 Beli: {{ $asset->purchase_date ? date('d M Y', strtotime($asset->purchase_date)) : '-' }}
                                    </div>
                                </td>

                                <td class="border p-2">
                                    <span class="px-2 py-1 rounded text-xs font-bold 
                                        {{ $asset->category == 'mobile' ? 'bg-purple-100 text-purple-800' : 
                                        ($asset->category == 'fixed' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800') }}">
                                        {{ ucfirst($asset->category) }}
                                    </span>
                                </td>
                                <td class="border p-2">
                                    @if($asset->status == 'available')
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-800">Available</span>
                                    @elseif($asset->status == 'in_use')
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-800">In Use</span>
                                    @elseif($asset->status == 'maintenance')
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-yellow-100 text-yellow-800">Maintenance</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-800">{{ ucfirst($asset->status) }}</span>
                                    @endif
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
                                <td colspan="6" class="border p-4 text-center text-gray-500">Belum ada data aset.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>