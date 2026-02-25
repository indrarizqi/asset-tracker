<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">Overview statistik detail inventaris Vodeco.</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Total Aset</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-1">{{ $totalAssets }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Total Nilai Aset</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-1">Rp 0</h3>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border {{ $pendingCount > 0 ? 'border-red-300 bg-red-50/30' : 'border-gray-200' }} p-5 shadow-sm transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold {{ $pendingCount > 0 ? 'text-red-600' : 'text-gray-500' }}">Pending Tickets (Approval)</p>
                            <h3 class="text-3xl font-extrabold {{ $pendingCount > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $pendingCount }}</h3>
                        </div>
                        <div class="p-3 {{ $pendingCount > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg">
                            <svg class="w-6 h-6 {{ $pendingCount > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mt-4">Sebaran Status Aset</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                
                <div class="bg-white border-l-4 border-blue-500 rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Available</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $statusAvailable }}</h4>
                </div>
                
                <div class="bg-white border-l-4 border-emerald-500 rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">In Use</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $statusInUse }}</h4>
                </div>
                
                <div class="bg-white border-l-4 border-amber-500 rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Maintenance</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $statusMaintenance }}</h4>
                </div>
                
                <div class="bg-white border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Broken</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $statusBroken }}</h4>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h4 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Kategori Aset
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-bold text-gray-600 uppercase">Mobile</span>
                            <span class="text-sm font-extrabold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">{{ $catMobile }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-bold text-gray-600 uppercase">Semi-Mobile</span>
                            <span class="text-sm font-extrabold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">{{ $catSemiMobile }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-bold text-gray-600 uppercase">Fixed</span>
                            <span class="text-sm font-extrabold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">{{ $catFixed }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h4 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        Kondisi Fisik
                    </h4>
                    <div class="space-y-3">
                        
                        <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-bold text-gray-600 uppercase">Baik</span>
                            <span class="text-sm font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">{{ $condBaik }}</span>
                        </div>

                        <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-bold text-gray-600 uppercase">Rusak</span>
                            <span class="text-sm font-extrabold text-orange-700 bg-orange-100 px-2 py-0.5 rounded">{{ $condRusak }}</span>
                        </div>

                        <div class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                            <span class="text-xs font-bold text-gray-600 uppercase">Rusak Total</span>
                            <span class="text-sm font-extrabold text-red-700 bg-red-100 px-2 py-0.5 rounded">{{ $condRusakTotal }}</span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-800">Activity History</h3>
                    </div>
                    <div class="p-6 flex-1">
                        @if($logs->count() > 0)
                            <div class="space-y-4">
                                @foreach($logs as $log)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-200 hover:shadow-sm transition-all bg-white">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-bold text-gray-900 text-sm">Aset {{ $log->asset->name ?? 'Unknown' }} {{ $log->action }}</h4>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->format('d/m/Y, h:i A') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed mb-2">
                                        Perubahan diajukan oleh <b>{{ $log->user->name ?? 'Sistem' }}</b>. Status log saat ini: 
                                        <span class="font-bold {{ $log->status === 'approved' ? 'text-emerald-600' : ($log->status === 'pending' ? 'text-amber-500' : 'text-red-500') }} uppercase">{{ $log->status }}</span>
                                    </p>
                                    <div class="text-[11px] text-gray-400 flex items-center gap-1">
                                        Action: <span class="uppercase font-bold">{{ $log->action }}</span> • Oleh: {{ $log->user->role ?? '-' }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 py-10">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p class="text-sm">Belum ada riwayat aktivitas.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-1 bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <h3 class="text-lg font-bold text-gray-800">Status & Quick Actions</h3>
                    </div>
                    <div class="p-4 flex-1">
                        <p class="text-xs text-gray-500 mb-4 px-2">Jalan pintas untuk aksi operasional.</p>
                        
                        <div class="space-y-3">
                            
                            <a href="#" class="flex items-center justify-between p-4 rounded-lg border {{ $pendingCount > 0 ? 'border-red-200 bg-red-50/50 hover:bg-red-100' : 'border-gray-200 hover:bg-gray-50' }} transition-colors group">
                                <div>
                                    <h4 class="text-sm font-bold {{ $pendingCount > 0 ? 'text-red-700' : 'text-gray-900' }}">Antrean Persetujuan</h4>
                                    <p class="text-[11px] {{ $pendingCount > 0 ? 'text-red-500' : 'text-gray-500' }} mt-0.5">Review tiket pengajuan Admin</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($pendingCount > 0)
                                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-sm">{{ $pendingCount }}</span>
                                    @endif
                                    <svg class="w-4 h-4 {{ $pendingCount > 0 ? 'text-red-500' : 'text-gray-400' }} group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>
                            </a>

                            <a href="{{ route('assets.index') }}" class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 group-hover:text-indigo-700">Manajemen Aset</h4>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Lihat tabel seluruh data aset</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>

                            <a href="{{ route('assets.create') }}" class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-colors group">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 group-hover:text-indigo-700">Tambah Aset Baru</h4>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Input data inventaris baru</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>

                        </div>

                        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-blue-500">💡</span>
                                <h5 class="text-xs font-bold text-blue-800">Tip Workflow</h5>
                            </div>
                            <p class="text-[11px] text-blue-600 leading-relaxed">
                                Pantau kotak <b>Antrean Persetujuan</b>. Setiap kotak tersebut berwarna merah, berarti ada Admin yang meminta validasi perubahan data atau penghapusan aset.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>