<div wire:poll.3s class="flex overflow-x-auto space-x-4 pb-4 snap-x hide-scrollbar">
    @forelse ($polis as $poli)
        @if ($poli->jadwalPoli->count() > 0)
            {{-- Card Poli --}}
            <div class="flex-none w-72 bg-white border border-gray-200 rounded-xl shadow-md snap-start overflow-hidden">
                
                {{-- Header Merah --}}
                <div class="bg-red-600 text-white px-4 py-2 flex justify-between items-center">
                    <h3 class="font-bold text-sm uppercase truncate">{{ $poli->nama_poli }}</h3>
                    <span class="bg-white text-red-600 text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                        {{ $poli->jadwalPoli->count() }} Antre
                    </span>
                </div>
                
                {{-- Daftar Pasien di dalam Card --}}
                <div class="p-3 max-h-48 overflow-y-auto space-y-2 bg-gray-50">
                    @foreach ($poli->jadwalPoli as $index => $antrean)
                        @php
                            $jadwal = $antrean->jadwalMcu;
                            $namaPasien = $jadwal->karyawan->nama_karyawan ?? $jadwal->pesertaMcu->nama_lengkap ?? $jadwal->nama_pasien ?? '-';
                            $isCalling = $antrean->status === 'Calling';
                        @endphp
                        
                        <a href="{{ route('qr-patient-detail', $jadwal->id) }}?tab=poli-{{ $poli->id }}" 
                           class="flex items-center p-2 rounded border shadow-sm transition-all duration-200 group block cursor-pointer {{ $isCalling ? 'bg-yellow-50 border-yellow-300 hover:bg-yellow-100' : 'bg-white border-gray-200 hover:border-red-300 hover:bg-red-50' }}">
                            
                            <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center text-xs font-bold mr-3 transition-colors {{ $isCalling ? 'bg-yellow-500 text-white' : 'bg-red-100 text-red-700 group-hover:bg-red-600 group-hover:text-white' }}">
                                {{ $index + 1 }}
                            </div>
                            
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-800 truncate group-hover:text-red-700 transition-colors">{{ $namaPasien }}</p>
                                <p class="text-[10px] text-gray-500 truncate">SAP/NIK: {{ $jadwal->no_sap ?? $jadwal->nik_pasien ?? 'Umum' }}</p>
                            </div>

                            @if($isCalling)
                            <div class="flex-shrink-0 ml-2" title="Sedang diperiksa di ruangan">
                                <span class="animate-pulse h-3 w-3 bg-yellow-500 rounded-full inline-block shadow"></span>
                            </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @empty
        <div class="w-full p-4 bg-gray-50 text-gray-500 text-sm text-center rounded-lg border border-dashed border-gray-300">
            Belum ada data poli yang tersedia.
        </div>
    @endforelse

    @if ($polis->sum(fn($p) => $p->jadwalPoli->count()) === 0)
        <div class="w-full p-4 bg-green-50 text-green-700 text-sm font-medium text-center rounded-lg border border-green-200 shadow-sm flex flex-col items-center justify-center">
            <i class="fas fa-check-circle text-2xl mb-2 text-green-500"></i>
            <p>Tidak ada antrean pasien di semua poli saat ini.</p>
        </div>
    @endif
</div>