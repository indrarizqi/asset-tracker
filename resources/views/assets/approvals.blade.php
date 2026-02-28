<x-app-layout>
    <x-slot name="title">Approvals Queue</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approvals Queue') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">Review dan validasi permintaan perubahan atau penghapusan aset dari Admin.</p>
    </x-slot>

    <div class="py-8" x-data="{ 
        showRejectModal: false, 
        rejectId: '', 
        rejectAction: '' 
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span class="font-semibold text-sm">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tiket Menunggu Validasi
                    </h3>
                    <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full shadow-inner">{{ $pendingLogs->total() }} Pending</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-gray-100 text-[11px] text-gray-400 uppercase tracking-widest">
                                <th class="px-6 py-4 font-bold">Waktu Pengajuan</th>
                                <th class="px-6 py-4 font-bold">Data Aset</th>
                                <th class="px-6 py-4 font-bold">Diajukan Oleh</th>
                                <th class="px-6 py-4 font-bold">Tipe Aksi</th>
                                <th class="px-6 py-4 font-bold text-right">Tindakan Eksekusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pendingLogs as $log)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-[11px] font-mono text-gray-500 mt-0.5">{{ $log->created_at->format('H:i:s A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-extrabold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $log->asset->name ?? 'Aset Dihapus / Hilang' }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $log->asset->asset_tag ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px]">{{ substr($log->user->name ?? 'S', 0, 1) }}</div>
                                        {{ $log->user->name ?? 'Sistem' }}
                                    </div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-1 pl-6">{{ str_replace('_', ' ', $log->user->role ?? '-') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->action === 'update')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700 uppercase border border-blue-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Edit Data
                                        </span>
                                    @elseif($log->action === 'delete')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-bold bg-red-50 text-red-700 uppercase border border-red-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus Aset
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-700 uppercase">{{ $log->action }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" 
                                            @click="showRejectModal = true; rejectId = '{{ $log->id }}'; rejectAction = '{{ strtoupper($log->action) }}'"
                                            class="inline-flex items-center px-4 py-2 bg-white border border-red-200 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50 hover:border-red-300 transition shadow-sm">
                                            Tolak
                                        </button>

                                        <form action="{{ route('approvals.approve', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin MENYETUJUI dan menerapkan perubahan ini ke database utama?');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 hover:border-emerald-700 transition shadow-sm">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Setujui
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4 shadow-inner border border-emerald-100">
                                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1">Antrean Kosong</h3>
                                    <p class="text-sm text-gray-500">Tidak ada permintaan yang menunggu untuk divalidasi.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($pendingLogs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $pendingLogs->links() }}
                </div>
                @endif
            </div>

        </div>

        <div x-show="showRejectModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div x-show="showRejectModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" @click="showRejectModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showRejectModal" x-transition class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full border border-gray-100 p-6">
                    
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0 border border-red-200">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Tolak Permintaan</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Anda akan menolak aksi <span x-text="rejectAction" class="font-bold text-red-600"></span> ini.</p>
                        </div>
                    </div>
                    
                    <form method="POST" :action="`/approvals/${rejectId}/reject`">
                        @csrf
                        <div class="mb-5 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">Catatan Penolakan (Opsional)</label>
                            <textarea name="rejection_note" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm" placeholder="Berikan alasan spesifik kepada Admin mengapa tiket ini ditolak..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-red-600 border border-red-600 rounded-lg text-sm font-bold text-white hover:bg-red-700 transition shadow-md">Tolak & Kembalikan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>