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

                <div class="mb-6 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-800">Pilih Aset untuk Dicetak</h3>
                    <p class="text-gray-500 text-sm">Gunakan kolom pencarian untuk menyaring data, lalu centang aset
                        yang ingin Anda cetak labelnya</p>
                </div>

                <form action="{{ route('assets.pdf') }}" method="GET" target="_blank" id="printForm">

                    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">

                        <div class="relative w-full sm:w-1/3">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="searchInput"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-800 focus:border-gray-800 block w-full pl-10 py-2.5"
                                placeholder="Cari ID, Nama, atau Kategori...">
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto sm:justify-end">
                            <button type="button" id="selectAllBtn" onclick="toggleSelectAll()"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition w-32 text-center">
                                Select All
                            </button>

                            <button type="submit" id="printSelectedBtn" disabled
                                class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Asset ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Asset Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Person in Charge & Info</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Asset Category</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border-l border-gray-200 w-24">
                                        Options</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="assetTableBody">
                                @foreach($assets as $asset)
                                    <tr class="hover:bg-gray-50 transition-colors asset-row cursor-pointer"
                                        onclick="toggleRowCheckbox(event, this)">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="font-mono font-bold text-gray-900 asset-id">{{ $asset->asset_tag }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 asset-name">
                                            {{ $asset->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $asset->person_in_charge ?? '-' }}
                                            <div class="text-xs text-gray-400">{{ $asset->purchase_date }}</div>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-600 asset-category">
                                            {{ ucfirst($asset->category) }}
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @php
                                                $statusKey = strtolower(str_replace(' ', '_', $asset->status));
                                                $colorClass = 'bg-gray-100 text-gray-700';

                                                if (str_contains($statusKey, 'broken')) {
                                                    $colorClass = 'bg-red-100 text-red-700';
                                                } elseif (str_contains($statusKey, 'use')) {
                                                    $colorClass = 'bg-blue-100 text-blue-700';
                                                } elseif (str_contains($statusKey, 'maintenance')) {
                                                    $colorClass = 'bg-yellow-100 text-yellow-700';
                                                }
                                            @endphp
                                            <span
                                                class="text-xs {{ $colorClass }} px-2 py-1 rounded border">{{ ucfirst($asset->status) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center border-l border-gray-100">
                                            <input type="checkbox" name="selected_assets[]" value="{{ $asset->id }}"
                                                class="asset-checkbox w-5 h-5 text-gray-800 rounded border-gray-400 focus:ring-gray-800 cursor-pointer transition">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div id="noResultMessage" class="hidden text-center py-8 text-gray-500 font-medium">
                            Aset tidak ditemukan.
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const printBtn = document.getElementById('printSelectedBtn');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.asset-row');
            const noResultMessage = document.getElementById('noResultMessage');

            // 1. Fungsi Update Status Tombol Print & Teks "Select All"
            function updateBtnState() {
                const anyChecked = Array.from(checkboxes).some(c => c.checked);
                printBtn.disabled = !anyChecked;

                // Cek apakah semua checkbox YANG TERLIHAT sudah dicentang
                const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('tr').style.display !== 'none');
                const allVisibleChecked = visibleCheckboxes.every(c => c.checked) && visibleCheckboxes.length > 0;

                // Ubah teks tombol secara otomatis
                if (allVisibleChecked && visibleCheckboxes.length > 0) {
                    selectAllBtn.textContent = 'Deselect All';
                } else {
                    selectAllBtn.textContent = 'Select All';
                }
            }

            // Pasang event listener ke setiap checkbox
            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBtnState);
            });

            // 2. Fungsi Select All (Tanpa Master Checkbox)
            window.toggleSelectAll = function () {
                const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('tr').style.display !== 'none');
                const allVisibleChecked = visibleCheckboxes.every(c => c.checked) && visibleCheckboxes.length > 0;

                // Jika semuanya sudah dicentang, maka lepas semua. Jika belum, centang semua.
                const targetState = !allVisibleChecked;

                visibleCheckboxes.forEach((cb) => {
                    cb.checked = targetState;
                });

                updateBtnState();
            };

            // 3. (Opsional/Bonus) Fungsi agar user bisa klik sembarang di baris tabel untuk mencentang
            window.toggleRowCheckbox = function (event, rowElement) {
                // Cegah klik ganda jika user memang mengeklik kotak checkbox-nya langsung
                if (event.target.type !== 'checkbox') {
                    const checkbox = rowElement.querySelector('.asset-checkbox');
                    checkbox.checked = !checkbox.checked;
                    updateBtnState();
                }
            };

            // 4. Fitur Live Search
            searchInput.addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                let visibleCount = 0;

                rows.forEach(row => {
                    const idText = row.querySelector('.asset-id').textContent.toLowerCase();
                    const nameText = row.querySelector('.asset-name').textContent.toLowerCase();
                    const categoryText = row.querySelector('.asset-category').textContent.toLowerCase();
                    const checkbox = row.querySelector('.asset-checkbox');

                    if (idText.includes(filter) || nameText.includes(filter) || categoryText.includes(filter)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        checkbox.checked = false;
                    }
                });

                noResultMessage.classList.toggle('hidden', visibleCount > 0);
                updateBtnState();
            });

            // Inisialisasi awal
            updateBtnState();
        });
    </script>
</x-app-layout>