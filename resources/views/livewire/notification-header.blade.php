{{-- Atribut wire:poll.10s menjalankan request asinkronus ke server tiap 10 detik untuk mengambil update notifikasi terbaru --}}
<div x-data="{ open: false }" wire:poll.10s="checkNotifications" class="relative">
    
    {{-- Tombol Lonceng untuk toggle menu dropdown --}}
    <button @click="open = !open" class="p-2 rounded-full text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors duration-150 relative focus:outline-none">
        <i class="fas fa-bell text-lg"></i> 
        @if ($unreadNotificationsCount > 0)
            <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-md animate-pulse border-2 border-white">
                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
            </span>
        @endif
    </button>

    {{-- max-w-[280px] digunakan agar dropdown tidak terlalu memakan ruang layar horizontal pada HP --}}
    <div x-show="open" 
         @click.away="open = false; $wire.markNotificationsAsRead()" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-[-10px] md:right-0 mt-3 w-[280px] md:w-80 bg-white rounded-xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] overflow-hidden z-50 border border-slate-100 origin-top-right"
         style="display: none;">
         
         <div class="p-3 border-b border-slate-100 bg-white flex justify-between items-center">
             <h4 class="text-xs font-black text-slate-800">Notifikasi Terbaru</h4>
             @if ($unreadNotificationsCount > 0)
                 <span class="bg-red-100 text-red-600 text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded">Baru</span>
             @endif
         </div>
         
        {{-- Ketinggian dibatasi max-h-60 (sekitar 240px) agar membatasi panjang scroll di dalam dropdown --}}
        <div class="max-h-60 overflow-y-auto custom-scrollbar">
            @if (count($latestNotifications) > 0)
                 <div class="flex flex-col divide-y divide-slate-50">
                     @foreach ($latestNotifications as $notif)
                        <a href="{{ route('jadwal.index') }}?search_sap={{ $notif->karyawan->no_sap ?? '' }}" 
                            class="p-3 transition-colors flex items-start gap-2 group cursor-pointer {{ $notif->is_read_admin ? 'bg-white opacity-80 hover:bg-slate-50' : 'bg-red-50/40 hover:bg-red-50' }}">
                             <div class="w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center shrink-0 group-hover:bg-red-500 group-hover:text-white transition-colors">
                                 <i class="fas fa-calendar-check text-[10px]"></i>
                             </div>
                             <div class="flex-1 min-w-0">
                                 <p class="text-[11px] md:text-xs text-slate-600 leading-snug truncate">
                                     <span class="font-black text-slate-800">{{ $notif->karyawan->nama_karyawan ?? 'Karyawan' }}</span>
                                 </p>
                                 <p class="text-[10px] text-slate-500 truncate">Jadwal: {{ \Carbon\Carbon::parse($notif->tanggal_mcu)->format('d/m/Y') }}</p>
                                 <p class="text-[9px] text-slate-400 font-bold mt-1"><i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}</p>
                             </div>
                         </a>
                     @endforeach
                 </div>
             @else
                 <div class="p-6 text-center flex flex-col items-center justify-center bg-slate-50">
                     <i class="fas fa-bell-slash text-2xl text-slate-300 mb-2"></i>
                     <p class="text-[10px] font-bold text-slate-500">Belum ada pengajuan jadwal baru.</p>
                 </div>
             @endif
         </div>

         <div class="p-2 border-t border-slate-100 bg-slate-50 text-center hover:bg-slate-100 transition-colors">
             <a href="{{ route('jadwal.index') }}" class="block text-[10px] font-black text-slate-500 hover:text-red-600 uppercase tracking-widest">
                 Buka Manajemen Jadwal &rarr;
             </a>
         </div>
    </div>
</div>