<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        showAssetModal: false, 
        asset: { 
            id: '', 
            name: '', 
            pic: '', 
            category: '', 
            status: '', 
            description: '', 
            edit_url: '' 
        } 
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 border border-gray-100">
                <div class="w-full sm:w-1/2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="search-input" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg leading-5 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition" 
                        placeholder="Cari aset berdasarkan ID, nama, atau kategori...">
                </div>
                <div class="flex gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600  rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add New
                    </a>
                    @if(auth()->user() && auth()->user()->role === 'super_admin')
                    <a href="{{ route('report.assets') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600  rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF Report
                    </a>
                    @endif
                </div>
            </div>

            <div id="table-container" class="w-full flex flex-col gap-4">
                @include('assets.partials.table') </div>
        </div>

        <div x-show="showAssetModal" 
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <div x-show="showAssetModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                 @click="showAssetModal = false"></div>

            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showAssetModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-gray-100">
                    
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-extrabold text-gray-900 flex items-center gap-2" id="modal-title">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            Asset Details
                        </h3>
                        <button @click="showAssetModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors cursor-pointer">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 gap-y-4">
                            
                            <div class="flex items-center gap-4 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
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
                                        <span x-text="asset.pic"></span>
                                    </span>
                                </div>
                                <div class="flex justify-between px-4 py-3">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Category</span>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase" x-text="asset.category"></span>
                                </div>
                                <div class="flex justify-between px-4 py-3">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</span>
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase" x-text="asset.status"></span>
                                </div>
                                <div class="flex flex-col px-4 py-3 bg-gray-50/50 rounded-b-lg">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Description</span>
                                    <p class="text-sm text-gray-600 leading-relaxed" x-text="asset.description || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <a :href="asset.edit_url" 
                           class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                           Edit
                        </a>
                        <button @click="showAssetModal = false" type="button" 
                                class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors cursor-pointer">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div> <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            const searchInput = document.getElementById('search-input');
            const tableContainer = document.getElementById('table-container');
            let typingTimer;                
            const doneTypingInterval = 500; // Debounce 500ms

            // 1. EVENT LISTENER INPUT
            searchInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(performSearch, doneTypingInterval);
            });

            // 2. FUNGSI AJAX SEARCH
            function performSearch() {
                let query = searchInput.value;
                let url = new URL(window.location.href);
                
                if(query) {
                    url.searchParams.set('search', query);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page'); // Reset ke halaman 1
                window.history.pushState({}, '', url);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
            }

            // 3. HANDLING PAGINATION VIA AJAX
            tableContainer.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    let pageUrl = e.target.closest('a').href;
                    window.history.pushState({}, '', pageUrl);

                    fetch(pageUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                        tableContainer.scrollIntoView({ behavior: 'smooth' });
                    });
                }
            });
        });
    </script>
</x-app-layout>