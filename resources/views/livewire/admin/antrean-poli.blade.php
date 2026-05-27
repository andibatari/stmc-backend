{{-- wire:poll.5s akan me-refresh komponen ini setiap 5 detik secara diam-diam di background --}}
<div wire:poll.5s class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Antrean Poli Real-Time</h1>
            <p class="text-sm text-gray-600">Daftar pasien yang sedang menunggu di depan ruangan.</p>
        </div>
        
        {{-- Dropdown Pilih Poli --}}
        <div class="w-64">
            <select wire:model.live="selectedPoliId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">-- Tampilkan Semua Poli --</option>
                @foreach($listPoli as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-red-800">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">No. Antrean</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama Pasien</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Jam Ambil</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($daftarAntrean as $index => $antrean)
                    @php
                        // Deteksi nama pasien (Karyawan atau Umum)
                        $namaPasien = $antrean->jadwal->karyawan->nama_karyawan ?? $antrean->jadwal->pesertaMcu->nama_lengkap ?? 'N/A';
                        $isCalling = $antrean->status === 'Calling';
                    @endphp
                    <tr class="{{ $isCalling ? 'bg-yellow-50' : 'hover:bg-gray-50' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-xl font-bold {{ $isCalling ? 'text-yellow-600' : 'text-gray-800' }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">{{ $namaPasien }}</div>
                            <div class="text-xs text-gray-500">{{ $antrean->poli->nama_poli ?? 'Poli' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $antrean->updated_at->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($isCalling)
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-200 text-yellow-800">Diperiksa</span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">Menunggu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($isCalling)
                                {{-- Jika sudah dipanggil, beri tombol untuk menuju halaman rekam medis (Detail) --}}
                                <a href="{{ route('jadwal.detail', $antrean->jadwal_id) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-colors">
                                    Input Hasil Medis &rarr;
                                </a>
                            @else
                                {{-- Jika masih menunggu, beri tombol Panggil --}}
                                <button wire:click="panggilPasien({{ $antrean->id }})" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors">
                                    <i class="fas fa-bullhorn mr-1"></i> Mulai Periksa
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-mug-hot text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Antrean Kosong</p>
                                <p class="text-sm">Belum ada pasien yang mengambil antrean di poli ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>