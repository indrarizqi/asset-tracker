<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Print QR Code Labels') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    
                    <div class="w-full sm:w-1/3 relative">
                        <form method="GET" action="{{ route('assets.print') }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-150 ease-in-out" 
                                placeholder="Cari ID, Nama, atau Kategori...">
                        </form>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <span id="selected-count" class="text-xs font-bold text-purple-700 bg-purple-50 px-3 py-2 rounded-md hidden transition-all">
                            0 Selected
                        </span>

                        <button type="button" id="btn-select-all" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Select All
                        </button>

                        <button type="button" id="btn-print" onclick="printSelected()" disabled 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="text-gray-500 text-[11px] font-bold uppercase tracking-wider border-b border-gray-200">
                                <th class="px-4 py-3 pb-4">Asset ID</th>
                                <th class="px-4 py-3 pb-4">Asset Name</th>
                                <th class="px-4 py-3 pb-4">Person In Charge & Info</th>
                                <th class="px-4 py-3 pb-4 text-center">Category</th>
                                <th class="px-4 py-3 pb-4 text-center">Status</th>
                                <th class="px-4 py-3 pb-4 text-center w-24">Options</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-bold text-gray-800 text-sm bg-gray-50 px-2 py-1 rounded border border-gray-200">{{ $asset->asset_tag }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 font-medium">Kondisi: {{ $asset->condition ?? 'Baik' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $asset->person_in_charge ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '' }}</div>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full {{ strtolower($asset->category) == 'fixed' ? 'bg-indigo-100 text-indigo-700' : 'bg-purple-100 text-purple-700' }} uppercase">
                                        {{ $asset->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @php
                                        $statusKey = strtolower(str_replace(' ', '_', $asset->status));
                                        $statusClass = 'bg-gray-100 text-gray-600 border border-gray-200';
                                        if (str_contains($statusKey, 'available')) $statusClass = 'bg-green-50 text-green-600 border border-green-200';
                                        elseif (str_contains($statusKey, 'maintenance')) $statusClass = 'bg-yellow-50 text-yellow-600 border border-yellow-200';
                                        elseif (str_contains($statusKey, 'use')) $statusClass = 'bg-blue-50 text-blue-600 border border-blue-200';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full uppercase {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $asset->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <input type="checkbox" value="{{ $asset->id }}" class="asset-checkbox w-5 h-5 rounded border-gray-300 text-gray-800 shadow-sm focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50 cursor-pointer transition-transform hover:scale-110">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div> <div class="mt-8 flex justify-center">
                {{ $assets->links('pagination.custom') }}
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedAssets = JSON.parse(localStorage.getItem('selectedAssets')) || [];
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const btnPrint = document.getElementById('btn-print');
            const btnSelectAll = document.getElementById('btn-select-all');
            const countDisplay = document.getElementById('selected-count');

            updateUI();

            checkboxes.forEach(cb => {
                if (selectedAssets.includes(cb.value)) cb.checked = true;
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        if (!selectedAssets.includes(this.value)) selectedAssets.push(this.value);
                    } else {
                        selectedAssets = selectedAssets.filter(id => id !== this.value);
                    }
                    saveAndRefresh();
                });
            });

            btnSelectAll.addEventListener('click', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                if (allChecked) {
                    checkboxes.forEach(cb => {
                        cb.checked = false;
                        selectedAssets = selectedAssets.filter(id => id !== cb.value);
                    });
                } else {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) {
                            cb.checked = true;
                            if (!selectedAssets.includes(cb.value)) selectedAssets.push(cb.value);
                        }
                    });
                }
                saveAndRefresh();
            });

            function saveAndRefresh() {
                localStorage.setItem('selectedAssets', JSON.stringify(selectedAssets));
                updateUI();
            }

            function updateUI() {
                if (selectedAssets.length > 0) {
                    countDisplay.innerText = selectedAssets.length + ' Selected';
                    countDisplay.classList.remove('hidden');
                    btnPrint.disabled = false;
                    btnPrint.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    countDisplay.classList.add('hidden');
                    btnPrint.disabled = true;
                    btnPrint.classList.add('opacity-50', 'cursor-not-allowed');
                }
                const allCheckedNow = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
                btnSelectAll.textContent = allCheckedNow ? "Deselect All" : "Select All";
            }

            window.printSelected = function() {
                if (selectedAssets.length === 0) return;
                let url = "{{ route('assets.pdf') }}"; 
                const params = new URLSearchParams();
                selectedAssets.forEach(id => params.append('selected_assets[]', id));
                window.open(url + '?' + params.toString(), '_blank');
            };
        });
    </script>
</x-app-layout>