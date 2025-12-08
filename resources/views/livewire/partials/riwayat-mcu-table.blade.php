{{-- File: livewire/partials/riwayat-mcu-table.blade.php --}}

@php
    // Variabel $user diasumsikan sudah tersedia di scope ini
    $riwayatMcu = $user->jadwalMcu ?? collect(); // Pastikan ini mengambil data riwayat MCU
@endphp

{{-- BLOK DESKTOP: Tampilan Tabel Tradisional --}}
<div class="overflow-x-auto hidden md:block">
    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tanggal MCU</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Dokter</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Status</th>
                <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @if ($riwayatMcu->count() > 0)
                @foreach ($riwayatMcu as $index => $jadwalMcu)
                <tr>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $jadwalMcu->tanggal_mcu }}</td>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $jadwalMcu->dokter->nama_lengkap ?? 'N/A' }}</td>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $jadwalMcu->status }}</td>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900 text-center">
                        <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        Tidak ada riwayat MCU.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- BLOK MOBILE: Card View (Muncul jika di bawah md) --}}
<div class="md:hidden space-y-3">
    @if ($riwayatMcu->count() > 0)
        @foreach($riwayatMcu as $index => $jadwalMcu)
            <div class="border border-gray-200 bg-white p-3 rounded-lg shadow-sm space-y-1 text-xs">
                <div class="flex justify-between border-b pb-1">
                    <span class="font-semibold text-gray-600">No:</span>
                    <span class="font-bold text-red-600">{{ $index + 1 }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="font-semibold text-gray-600">Tanggal MCU:</span>
                    <span>{{ $jadwalMcu->tanggal_mcu }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="font-semibold text-gray-600">Dokter:</span>
                    <span class="truncate max-w-[50%]">{{ $jadwalMcu->dokter->nama_lengkap ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center pt-1">
                    <span class="font-semibold text-gray-600">Status:</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold 
                        @if($jadwalMcu->status === 'Scheduled') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ $jadwalMcu->status ?? 'N/A' }}
                    </span>
                </div>
                {{-- Tombol Aksi --}}
                <div class="text-right pt-2 border-t mt-2">
                    <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">Lihat Detail &raquo;</a>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center text-gray-500 text-sm p-4 bg-white rounded-lg">Tidak ada riwayat MCU.</div>
    @endif
</div>