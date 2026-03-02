<x-app-layout>
    <x-slot name="title">Asset Management</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asset Management') }}
        </h2>
    </x-slot>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-8" x-data="{ 
        showAssetModal: false, 
        showScannerModal: false, 
        showActionModal: false,
        asset: { id: '', name: '', pic: '', category: '', status: '', condition: '', price: '', purchase_date: '', location: '', vendor: '', serial_number: '', warranty_expiry_date: '', description: '', edit_url: '' },
        actionData: { id: '', tag: '', name: '', status: '' }
    }"
    @keydown.window.escape="showAssetModal = false; showScannerModal = false; showActionModal = false">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex flex-col gap-4 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3 w-full">
                    <div class="relative lg:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="search-input" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out" 
                        placeholder="Cari aset berdasarkan ID, nama, atau kategori...">
                    </div>

                    <select id="status-filter" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="available" @selected(request('status') === 'available')>Available</option>
                        <option value="in_use" @selected(request('status') === 'in_use')>In Use</option>
                        <option value="maintenance" @selected(request('status') === 'maintenance')>Maintenance</option>
                        <option value="broken" @selected(request('status') === 'broken')>Broken</option>
                    </select>

                    <select id="category-filter" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Kategori</option>
                        <option value="mobile" @selected(request('category') === 'mobile')>Mobile</option>
                        <option value="semi-mobile" @selected(request('category') === 'semi-mobile')>Semi-Mobile</option>
                        <option value="fixed" @selected(request('category') === 'fixed')>Fixed</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <input type="date" id="date-from" value="{{ request('date_from') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Tanggal pembelian mulai">
                        <input type="date" id="date-to" value="{{ request('date_to') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Tanggal pembelian akhir">
                    </div>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('assets.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition shadow-md">
                        Riwayat
                    </a>
                    
                    <button type="button" @click="showScannerModal = true; startScanner();" class="inline-flex items-center px-4 py-2 bg-emerald-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:border-emerald-900 focus:ring ring-emerald-300 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-18m0 0h2m-2-11h2m14 0h2M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 5h1v1H9v-1zm5 0h1v1h-1v-1zM7 5h4v4H7V5zm8 0h4v4h-4V5zm-8 8h4v4H7v-4zm8 0h4v4h-4v-4z"></path></svg>
                        Scan QR
                    </button>

                    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add New
                    </a>

                    @if(auth()->user() && in_array(auth()->user()->role, ['super_admin', 'admin']))
                    <a href="{{ route('report.assets') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF Report
                    </a>
                    @endif
                </div>
            </div>

            <div id="table-container" class="w-full flex flex-col gap-4">
                @include('assets.partials.table') 
            </div>
        </div>

        <div x-show="showAssetModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div x-show="showAssetModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="showAssetModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showAssetModal" x-transition class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-gray-100">
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Detail Aset
                        </h3>
                        <button @click="showAssetModal = false" class="text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex items-center gap-4 bg-indigo-50 p-4 rounded-lg border border-indigo-100 mb-4">
                            <div class="shrink-0 h-14 w-14 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-md">
                                <span x-text="asset.id.substring(0,2).toUpperCase()"></span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider" x-text="asset.id"></p>
                                <p class="text-lg font-bold text-gray-900 leading-tight" x-text="asset.name"></p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-0 divide-y divide-gray-100">
                            <div class="flex flex-col sm:flex-row sm:justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-0">Person in Charge</span>
                                <span class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span x-text="asset.pic || '-'"></span>
                                </span>
                            </div>
                            
                            <div class="flex justify-between px-4 py-3 bg-gray-50/30">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tgl. Pembelian</span>
                                <span class="text-sm font-semibold text-gray-700" x-text="asset.purchase_date"></span>
                            </div>

                            <div class="flex justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nilai Aset</span>
                                <span class="text-sm font-extrabold text-emerald-600" x-text="asset.price"></span>
                            </div>

                            <div class="flex justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</span>
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase" x-text="asset.category"></span>
                            </div>

                            <div class="flex justify-between px-4 py-3 bg-gray-50/30">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kondisi Fisik</span>
                                <span class="text-sm font-bold text-gray-800 uppercase" x-text="asset.condition"></span>
                            </div>

                            <div class="flex justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lokasi</span>
                                <span class="text-sm font-semibold text-gray-700" x-text="asset.location || '-' "></span>
                            </div>

                            <div class="flex justify-between px-4 py-3 bg-gray-50/30">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Vendor</span>
                                <span class="text-sm font-semibold text-gray-700" x-text="asset.vendor || '-' "></span>
                            </div>

                            <div class="flex justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Serial Number</span>
                                <span class="text-sm font-semibold text-gray-700" x-text="asset.serial_number || '-' "></span>
                            </div>

                            <div class="flex justify-between px-4 py-3 bg-gray-50/30">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Garansi Sampai</span>
                                <span class="text-sm font-semibold text-gray-700" x-text="asset.warranty_expiry_date || '-' "></span>
                            </div>

                            <div class="flex justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</span>
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase" x-text="asset.status"></span>
                            </div>

                            <div class="flex flex-col px-4 py-3 bg-gray-50/50 rounded-b-lg">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi</span>
                                <p class="text-sm text-gray-600 leading-relaxed" x-text="asset.description || '-'"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <a :href="asset.edit_url" class="inline-flex justify-center rounded-lg px-5 py-2.5 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors">
                            Edit
                        </a>
                        <button @click="showAssetModal = false" type="button" class="inline-flex justify-center rounded-lg border border-gray-300 px-5 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showActionModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div x-show="showActionModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="showActionModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showActionModal" x-transition class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full border border-gray-100 p-6">
                    <h3 class="text-lg font-extrabold text-gray-900 mb-2">Update Status Aset</h3>
                    <p class="text-sm text-gray-500 mb-4">Aset: <span class="font-bold text-indigo-600" x-text="actionData.name"></span> (<span x-text="actionData.tag"></span>)</p>
                    
                    <form id="form-update-status" method="POST" action="{{ route('assets.update-status') }}">
                        @csrf
                        <input type="hidden" name="asset_tag" :value="actionData.tag">
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aksi (Update Menjadi)*</label>
                            <select name="action" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition cursor-pointer">
                                <option value="" disabled selected>Pilih Status Baru...</option>
                                <option value="check_out">Pinjam / Gunakan (Check-out)</option>
                                <option value="check_in">Kembalikan (Check-in)</option>
                                <option value="maintenance">Kirim ke Maintenance</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Peminjam</label>
                                <input type="text" name="borrower_name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Opsional (saat check-out)">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Jatuh Tempo</label>
                                <input type="date" name="due_at" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Opsional"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showActionModal = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 rounded-lg text-sm font-bold text-white hover:bg-emerald-700 transition shadow-md">Simpan Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showScannerModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div x-show="showScannerModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showScannerModal" x-transition class="relative bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Arahkan Kamera ke QR Code
                        </h3>
                        <button @click="stopScanner(); showScannerModal = false" class="text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div id="reader" class="w-full rounded-lg overflow-hidden border-2 border-dashed border-gray-300 bg-black"></div>
                        <p class="text-center text-sm text-gray-500 mt-4">Pastikan ruangan cukup terang dan kamera fokus.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // --- 1. AJAX LIVE SEARCH & PAGINATION ---
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');
            const categoryFilter = document.getElementById('category-filter');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');
            const tableContainer = document.getElementById('table-container');
            let typingTimer;                
            const doneTypingInterval = 500; 

            searchInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(performSearch, doneTypingInterval);
            });

            [statusFilter, categoryFilter, dateFrom, dateTo].forEach((element) => {
                element.addEventListener('change', performSearch);
            });

            function performSearch() {
                let url = new URL(window.location.href);
                const query = searchInput.value;

                if(query) url.searchParams.set('search', query);
                else url.searchParams.delete('search');

                if (statusFilter.value) url.searchParams.set('status', statusFilter.value);
                else url.searchParams.delete('status');

                if (categoryFilter.value) url.searchParams.set('category', categoryFilter.value);
                else url.searchParams.delete('category');

                if (dateFrom.value) url.searchParams.set('date_from', dateFrom.value);
                else url.searchParams.delete('date_from');

                if (dateTo.value) url.searchParams.set('date_to', dateTo.value);
                else url.searchParams.delete('date_to');

                url.searchParams.delete('page'); 
                window.history.pushState({}, '', url);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => { tableContainer.innerHTML = html; })
                .catch(error => console.error('Error:', error));
            }

            tableContainer.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    let pageUrl = e.target.closest('a').href;
                    window.history.pushState({}, '', pageUrl);

                    fetch(pageUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                        tableContainer.scrollIntoView({ behavior: 'smooth' });
                    });
                }
            });
        });

        // --- 2. LOGIKA KAMERA SCANNER ---
        let html5QrcodeScanner = null;

        function startScanner() {
            if(!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
            }
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function stopScanner() {
            if(html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(error => console.error("Gagal mematikan kamera", error));
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            stopScanner(); // Matikan kamera
            document.querySelector('[x-data]').__x.$data.showScannerModal = false; // Tutup Modal Kamera
            
            // Buka Modal Action Update Status Otomatis
            let xData = document.querySelector('[x-data]').__x.$data;
            xData.actionData = { tag: decodedText, name: 'Aset Ditemukan', status: '' };
            xData.showActionModal = true;
        }

        function onScanFailure(error) {
            // Biarkan kamera terus mencari
        }
    </script>
</x-app-layout>