{{-- Tambahkan wire:poll agar mengecek database otomatis setiap 10 detik --}}
<div x-data="{ open: false }" wire:poll.10s="checkNotifications" class="relative">
    
    {{-- Tombol Lonceng Notifikasi --}}
    <button @click="open = !open; $wire.markNotificationsAsRead()" 
            class="p-2 rounded-full text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors duration-150 relative focus:outline-none">
        
        <i class="fas fa-bell text-xl"></i> {{-- Menggunakan FontAwesome agar senada dengan desain web kamu --}}
        
        {{-- Badge Notifikasi Aktif --}}
        @if ($unreadNotificationsCount > 0)
            <span class="absolute top-0 right-0 h-5 w-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center -mt-1 -mr-1 shadow-md animate-pulse">
                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel Notifikasi --}}
    {{-- Dropdown Panel Notifikasi --}}
    <div x-show="open" 
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] overflow-hidden z-50 border border-slate-100"
         style="display: none;">
         
         {{-- HEADER NOTIFIKASI --}}
         <div class="p-4 border-b border-slate-100 bg-white flex justify-between items-center">
             <h4 class="text-sm font-bold text-slate-800">Notifikasi Terbaru</h4>
             
             {{-- Badge "Baru" hanya muncul jika ada yang belum diproses --}}
             @if ($unreadNotificationsCount > 0)
                 <span class="bg-red-100 text-red-500 text-[10px] font-bold px-2 py-1 rounded-full">Baru</span>
             @endif
         </div>
         
         {{-- ISI NOTIFIKASI --}}
         <div class="max-h-72 overflow-y-auto">
             @if ($unreadNotificationsCount > 0)
                 {{-- Menampilkan Ringkasan Jadwal (Angka Dinamis) --}}
                 <a href="{{ route('jadwal.index') }}" class="p-4 hover:bg-slate-50 transition-colors flex items-start gap-3">
                     <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                         <i class="fas fa-calendar-check text-sm"></i>
                     </div>
                     <div>
                         <p class="text-sm font-bold text-slate-800">
                             {{ $unreadNotificationsCount }} Permintaan Jadwal Baru!
                         </p>
                         <p class="text-xs text-slate-500 mt-1">
                             {{-- Mengambil waktu dari data yang paling baru masuk --}}
                             {{ $latestNotifications->first() ? $latestNotifications->first()->created_at->diffForHumans() : 'Baru saja' }}
                         </p>
                     </div>
                 </a>
                 
                 <div class="p-4 text-center border-t border-slate-50">
                     <p class="text-[11px] text-slate-400">Tidak ada notifikasi lain.</p>
                 </div>
             @else
                 {{-- Tampilan saat 0 Notifikasi --}}
                 <div class="p-8 text-center flex flex-col items-center justify-center">
                     <i class="fas fa-bell-slash text-3xl text-slate-200 mb-3"></i>
                     <p class="text-sm font-medium text-slate-500">Belum ada pengajuan jadwal.</p>
                 </div>
             @endif
         </div>

         {{-- FOOTER --}}
         <div class="p-4 border-t border-slate-100 bg-white text-center">
             <a href="{{ route('jadwal.index') }}" class="inline-block text-xs font-bold text-red-600 hover:text-red-700 transition-colors">
                 Lihat Semua Notifikasi <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
             </a>
         </div>
    </div>
</div>