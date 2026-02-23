<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Aplikasi' }} - {{ config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        
        <div class="flex h-screen overflow-hidden">
            
            @include('layouts.navigation')

            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden ml-64 transition-all duration-200">
                
                @if (isset($header))
                    <header class="bg-white shadow-sm sticky top-0 z-10 px-8 py-3 flex items-center justify-between border-b border-gray-200">
                        
                        <div class="font-bold text-xl text-gray-800 leading-tight">
                            {{ $header }}
                        </div>
                        
                        <div class="flex flex-col items-end">
                            <div id="realtime-date" class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                Loading Date...
                            </div>

                            <div class="flex items-center bg-indigo-50 text-indigo-700 font-bold' px-3 py-3 rounded-lg">
                                <svg class="w-3 h-3 text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                
                                <span id="realtime-time" class="text-lg font-mono font-bold tracking-widest leading-none">
                                    00:00
                                </span>
                            </div>
                        </div>
                    </header>
                @endif

                <main class="p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <input type="hidden" id="global-flash-success" value="{{ session('success') }}">
        <input type="hidden" id="global-flash-error" value="{{ session('error') }}">

        <script>
            // Realtime Clock
            function updateClock() {
            const now = new Date();
            
            // Date Format
            const dateOptions = { 
                weekday: 'long', 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            };
            const dateString = now.toLocaleDateString('id-GB', dateOptions);

            // Time Format
            const timeOptions = { 
                hour: '2-digit', 
                minute: '2-digit', 
                hour24: true 
            };
            const timeString = now.toLocaleTimeString('en-GB', timeOptions);

            // Masukkan ke elemen HTML
            const dateEl = document.getElementById('realtime-date');
            const timeEl = document.getElementById('realtime-time');

            if (dateEl) dateEl.innerText = dateString;
            if (timeEl) timeEl.innerText = timeString; // Contoh output: 14:30
        }

            // Update setiap 1 detik (agar pergantian menit tetap akurat)
            setInterval(updateClock, 1000);
            updateClock(); // Jalankan langsung saat load

            // Global Sweet Alert
            document.addEventListener('DOMContentLoaded', function () {
                
                // 1. GLOBAL TOAST MIXIN (Konfigurasi Animasi Toast)
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    iconColor: 'white',
                    customClass: {
                        popup: 'colored-toast'
                    },
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer) // Animasi stop saat di-hover
                        toast.addEventListener('mouseleave', Swal.resumeTimer) // Lanjut saat mouse keluar
                    },
                    // ANIMASI MASUK & KELUAR (Lebih Hidup)
                    showClass: {
                        popup: 'swal2-show',
                        backdrop: 'swal2-backdrop-show',
                        icon: 'swal2-icon-show'
                    },
                    hideClass: {
                        popup: 'swal2-hide',
                        backdrop: 'swal2-backdrop-hide',
                        icon: 'swal2-icon-hide'
                    }
                });

                // 2. CEK FLASH MESSAGE DARI SESSION
                const successMsg = document.getElementById('global-flash-success')?.value;
                const errorMsg = document.getElementById('global-flash-error')?.value;

                if (successMsg) {
                    Toast.fire({
                        icon: 'success',
                        title: successMsg,
                        background: '#4f46e5',
                        color: '#fff'
                    });
                }

                if (errorMsg) {
                    Toast.fire({
                        icon: 'error',
                        title: errorMsg,
                        background: '#ef4444', 
                        color: '#fff'
                    });
                }

                // 3. GLOBAL EVENT LISTENER (Event Delegation untuk AJAX)
                document.addEventListener('submit', function(e) {
                    
                    // A. HANDLER LOGOUT
                    if (e.target && e.target.classList.contains('form-logout')) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi Logout',
                            text: "Pastikan aktivitas Anda sudah disimpan!",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#4f46e5', 
                            cancelButtonColor: '#9ca3af',  
                            confirmButtonText: 'Logout',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            focusCancel: true,
                            // ANIMASI POPUP (Zoom In)
                            showClass: {
                                popup: 'animate__animated animate__zoomIn faster' 
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut faster'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) e.target.submit();
                        });
                    }

                    // B. HANDLER DELETE (Global)
                    if (e.target && e.target.classList.contains('form-delete')) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            text: "Data akan dihapus secara permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            focusCancel: true,
                            // ANIMASI POPUP (Shake jika warning)
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tampilkan Loading saat proses hapus berjalan
                                Swal.fire({
                                    title: 'Sedang Menghapus...',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    }
                                });
                                e.target.submit();
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>