<aside class="fixed inset-y-0 left-0 z-20 w-64 bg-white border-r border-gray-200 flex flex-col h-screen transition-transform duration-300 ease-in-out transform translate-x-0">
    
    <div class="h-20 flex items-center px-6 border-b border-gray-100">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/Master-Logo-Vodeco.png') }}" alt="Logo Perusahaan" class="h-10 w-auto object-contain">
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 group {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span>Dashboard</span>
        </a>

        @if(Auth::user()->role === 'super_admin') 
        <a href="{{ route('users.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 group {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span>Users</span>
        </a>
        @endif

        <a href="{{ route('assets.print') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 group {{ request()->routeIs('assets.print*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('assets.print*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            <span>Print QR Code</span>
        </a>
    </nav>

    <div class="border-t border-gray-200 p-4 bg-gray-50/50">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold shadow-md">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            
            <div class="overflow-hidden">
                <div class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="form-logout">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </button>
        </form>
    </div>

</aside>