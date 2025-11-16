<div class="overflow-x-auto">
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
            @if ($user->jadwalMcu->count() > 0)
                @foreach ($user->jadwalMcu as $index => $jadwalMcu)
                <tr>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $jadwalMcu->tanggal_mcu }}</td>
                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $jadwalMcu->dokter->nama_lengkap }}</td>
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