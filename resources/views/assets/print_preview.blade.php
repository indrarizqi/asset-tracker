<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Aset</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">🖨️ Cetak Label QR Code</h2>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('assets.pdf') }}" method="GET" target="_blank" id="printForm">
            
            <div class="mb-4 flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Yang Dipilih
                </button>

                <button type="button" onclick="selectAll()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow">
                    Pilih Semua
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 border-b text-center w-12">#</th>
                            <th class="py-3 px-4 border-b text-left">ID Tag</th>
                            <th class="py-3 px-4 border-b text-left">Nama Aset</th>
                            <th class="py-3 px-4 border-b text-left">PJ & Tanggal</th>
                            <th class="py-3 px-4 border-b text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="py-2 px-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $asset->id }}" class="w-5 h-5 text-green-600 rounded cursor-pointer asset-checkbox">
                            </td>
                            <td class="py-2 px-4 font-mono font-bold">{{ $asset->asset_tag }}</td>
                            <td class="py-2 px-4">{{ $asset->name }}</td>
                            <td class="py-2 px-4 text-sm">
                                <div class="font-semibold">{{ $asset->person_in_charge ?? '-' }}</div>
                                <div class="text-gray-500 text-xs">{{ $asset->purchase_date }}</div>
                            </td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 text-xs rounded bg-gray-200">{{ $asset->asset_condition }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data aset.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <script>
        function selectAll() {
            const checkboxes = document.querySelectorAll('.asset-checkbox');
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => c.checked = !allChecked);
        }

        document.getElementById('printForm').addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.asset-checkbox:checked').length;
            if (checked === 0) {
                e.preventDefault();
                alert('Pilih minimal satu aset untuk dicetak!');
            }
        });
    </script>
</body>
</html>