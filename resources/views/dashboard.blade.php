<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
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
                    <a href="{{ route('report.assets') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600  rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        PDF Report
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div id="table-container" class="overflow-x-auto bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] pb-4">
                    @include('assets.partials.table')
                </div> 
            </div>
        </div>
    </div>

    <script>
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
                    // TIDAK PERLU lagi memanggil initDeleteListener() disini
                    // karena app.blade.php sudah menanganinya secara otomatis via Event Delegation
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