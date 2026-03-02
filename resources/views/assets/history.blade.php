<x-app-layout>
    <x-slot name="title">Riwayat Peminjaman Aset</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman & Pengembalian') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-4 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('assets.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / aset / tag"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="borrowed" @selected(request('status') === 'borrowed')>Borrowed</option>
                        <option value="returned" @selected(request('status') === 'returned')>Returned</option>
                        <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                    </select>

                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 text-white font-semibold text-xs uppercase rounded-lg hover:bg-indigo-700 transition">
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white overflow-hidden sm:rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-[11px] font-extrabold uppercase tracking-wider">
                                <th class="px-4 py-4">Aset</th>
                                <th class="px-4 py-4">Peminjam</th>
                                <th class="px-4 py-4">Dipinjam</th>
                                <th class="px-4 py-4">Jatuh Tempo</th>
                                <th class="px-4 py-4">Dikembalikan</th>
                                <th class="px-4 py-4">Durasi</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-4 py-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-blue-50/40 transition-colors duration-200">
                                    <td class="px-4 py-4 text-sm">
                                        <div class="font-bold text-gray-900">{{ $transaction->asset->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $transaction->asset->asset_tag ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $transaction->borrower_name }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $transaction->borrowed_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $transaction->due_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $transaction->returned_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $transaction->duration_days ? $transaction->duration_days . ' hari' : '-' }}</td>
                                    <td class="px-4 py-4">
                                        @php
                                            $statusClass = match($transaction->status) {
                                                'borrowed' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                                'returned' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                                'overdue' => 'bg-red-100 text-red-700 border border-red-200',
                                                default => 'bg-gray-100 text-gray-700 border border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase rounded-full {{ $statusClass }}">{{ $transaction->status }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($transactions->hasPages())
                <div class="mt-2 px-1">
                    {{ $transactions->links('pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
