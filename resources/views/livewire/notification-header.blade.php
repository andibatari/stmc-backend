<div x-data="{ open: false }" class="relative">
    {{-- Tombol Lonceng Notifikasi --}}
    {{-- Klik tombol akan memicu markNotificationsAsRead untuk mereset counter --}}
    <button @click="open = !open; @this.markNotificationsAsRead()" 
            class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-150 relative focus:outline-none">
        
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        
        {{-- Badge Notifikasi Aktif --}}
        @if ($unreadNotificationsCount > 0)
            <span class="absolute top-0 right-0 h-5 w-5 bg-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center -mt-1 -mr-1 shadow-md">
                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel Notifikasi --}}
    <div x-show="open" 
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl overflow-hidden z-20 border border-gray-200"
         style="display: none;">
         
         <div class="p-4 border-b bg-gray-50">
             <h4 class="text-sm font-bold text-gray-800">Permintaan Jadwal Baru ({{ $unreadNotificationsCount }})</h4>
         </div>
         
         {{-- Daftar Notifikasi Placeholder --}}
         <div class="max-h-64 overflow-y-auto">
             @if ($unreadNotificationsCount > 0)
                 <div class="p-4 text-center text-sm text-gray-600 border-b">
                    Terdapat **{{ $unreadNotificationsCount }}** pasien yang mengajukan jadwal.
                 </div>
                 <a href="{{ route('jadwal.index') }}" class="block px-4 py-3 hover:bg-red-50 text-sm font-semibold text-red-600 text-center">
                    Tinjau Sekarang
                 </a>
             @else
                 <div class="p-4 text-center text-sm text-gray-500">
                     Tidak ada permintaan jadwal baru.
                 </div>
             @endif
         </div>
    </div>
</div>
