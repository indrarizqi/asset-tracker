<x-app-layout>
    <x-slot name="title">Print QR Code</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Print QR Code Labels') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 border-b pb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pilih Aset untuk Dicetak</h3>
                        <p class="text-gray-500 text-sm">Centang aset yang ingin Anda cetak labelnya, atau gunakan tombol di kanan untuk cetak semua.</p>
                    </div>
                    
                    <a href="{{ route('assets.pdf') }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                         Print All
                    </a>
                </div>

                <form action="{{ route('assets.pdf') }}" method="GET" target="_blank" id="printForm">
                    
                    <div class="mb-4 flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
                            Cetak Yang Dipilih (Checklist)
                        </button>
                        
                        <button type="button" onclick="toggleSelectAll()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition">
                            Pilih / Lepas Semua
                        </button>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center w-10">
                                        <input type="checkbox" id="masterCheckbox" onclick="toggleSelectAll()">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Tag</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Aset</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">PJ & Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assets as $asset)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}" class="asset-checkbox w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $asset->asset_tag }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $asset->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $asset->person_in_charge ?? '-' }}
                                        <div class="text-xs text-gray-400">{{ $asset->purchase_date }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-bold rounded {{ $asset->status == 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const master = document.getElementById('masterCheckbox');
            // Jika tombol dipencet (master null) atau master checkbox dipencet
            const currentState = master.checked;
            
            checkboxes.forEach((cb) => {
                cb.checked = currentState;
            });
        }
        
        // Agar tombol "Pilih/Lepas Semua" juga memicu checkbox master
        document.querySelector('button[onclick="toggleSelectAll()"]').addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah submit form
            const master = document.getElementById('masterCheckbox');
            master.checked = !master.checked;
            toggleSelectAll();
        });
    </script>
</x-app-layout>