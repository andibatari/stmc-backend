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
         
         <div class="p-4 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
             <h4 class="text-sm font-black text-slate-700">Permintaan Jadwal</h4>
             <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded-md">{{ $unreadNotificationsCount }} Baru</span>
         </div>
         
         {{-- ISI NOTIFIKASI INDIVIDU --}}
         <div class="max-h-72 overflow-y-auto custom-scrollbar">
             @if ($unreadNotificationsCount > 0)
                 <div class="flex flex-col divide-y divide-slate-50">
                     @foreach ($latestNotifications as $notif)
                         {{-- Link mengarah ke jadwal dengan pencarian otomatis --}}
                         <a href="{{ route('jadwal.index') }}?search={{ $notif->karyawan->no_sap ?? '' }}" class="p-4 hover:bg-blue-50/50 transition-colors flex items-start gap-3 group">
                             
                             {{-- Ikon Lonceng Kecil --}}
                             <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                                 <i class="fas fa-bell text-xs animate-wiggle"></i>
                             </div>
                             
                             {{-- Teks Notifikasi --}}
                             <div class="flex-1">
                                 <p class="text-xs text-slate-600 leading-relaxed">
                                     <span class="font-bold text-slate-900">{{ $notif->karyawan->nama_karyawan ?? 'Karyawan' }}</span> baru saja mengajukan jadwal MCU untuk tanggal <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($notif->tanggal_mcu)->format('d M Y') }}</span>.
                                 </p>
                                 
                                 <div class="flex items-center justify-between mt-2">
                                     <p class="text-[9px] text-slate-400 font-medium">
                                         <i class="far fa-clock mr-1"></i> {{ $notif->created_at->diffForHumans() }}
                                     </p>
                                     <p class="text-[10px] text-blue-600 font-bold flex items-center gap-1 group-hover:text-blue-800 transition-colors">
                                         Lihat <i class="fas fa-arrow-right text-[8px]"></i>
                                     </p>
                                 </div>
                             </div>
                         </a>
                     @endforeach
                 </div>
             @else
                 <div class="p-8 text-center flex flex-col items-center justify-center">
                     <i class="fas fa-check-circle text-4xl text-slate-200 mb-3"></i>
                     <p class="text-sm font-bold text-slate-500">Semua jadwal sudah diproses.</p>
                     <p class="text-xs text-slate-400 mt-1">Tidak ada pengajuan baru.</p>
                 </div>
             @endif
         </div>

         {{-- FOOTER --}}
         @if ($unreadNotificationsCount > 0)
         <div class="p-3 border-t border-slate-100 bg-slate-50 text-center">
             <a href="{{ route('jadwal.index') }}" class="inline-block text-[11px] font-bold text-slate-500 hover:text-blue-600 transition-colors uppercase tracking-wider">
                 Buka Manajemen Jadwal <i class="fas fa-external-link-square-alt ml-1"></i>
             </a>
         </div>
         @endif
    </div>
</div>