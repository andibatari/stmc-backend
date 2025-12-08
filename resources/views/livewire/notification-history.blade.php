{{-- Ganti seluruh konten di livewire/notification-dashboard.blade.php dengan ini --}}

<div class="max-w-7xl mx-auto py-4 px-2 sm:px-4 md:py-8 md:px-6">
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
        
        <h3 class="text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-history text-gray-600 mr-2"></i> Riwayat Pengiriman Notifikasi
        </h3>

        {{-- Blok Desktop: Tabel Lebar (Hidden di Mobile) --}}
        <div class="hidden md:block">
            <div class="overflow-x-auto bg-white border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Kirim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Jadwal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Target</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sukses Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sukses App (FCM)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin/Sistem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->scheduled_date)->format('d F Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($log->mode === 'automatic') bg-indigo-100 text-indigo-800 @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($log->mode) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $log->total_targets }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600">{{ $log->email_success }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600">{{ $log->fcm_success }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->admin->name ?? 'SYSTEM' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada riwayat pengiriman notifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Blok Mobile: Card View (Muncul di Mobile) --}}
        <div class="md:hidden space-y-4">
            @forelse ($logs as $log)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 text-xs">
                    
                    {{-- Baris 1: Tanggal Kirim & Mode --}}
                    <div class="flex justify-between items-center border-b pb-2 mb-2">
                        <span class="font-bold text-gray-800">{{ $log->created_at->format('d M Y') }}</span>
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full @if($log->mode === 'automatic') bg-indigo-100 text-indigo-800 @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($log->mode) }}
                        </span>
                    </div>

                    {{-- Baris 2: Target Jadwal --}}
                    <p class="mb-2">
                        <span class="font-semibold text-gray-600 block">Target Tgl Jadwal:</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($log->scheduled_date)->format('d F Y') }}</span>
                    </p>

                    {{-- Baris 3: Status Sukses --}}
                    <div class="grid grid-cols-3 gap-2 border-t pt-2">
                        <div>
                            <span class="font-semibold text-gray-600 block">Total:</span>
                            <span class="text-sm font-bold text-gray-800">{{ $log->total_targets }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-600 block">Email Sukses:</span>
                            <span class="text-sm font-bold text-green-600">{{ $log->email_success }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-600 block">App Sukses:</span>
                            <span class="text-sm font-bold text-green-600">{{ $log->fcm_success }}</span>
                        </div>
                    </div>

                    {{-- Baris 4: Admin --}}
                    <p class="mt-2 text-gray-500 text-xs">
                        Dikirim oleh: {{ $log->admin->name ?? 'SYSTEM' }}
                    </p>
                </div>
            @empty
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-sm" role="alert">
                    Belum ada riwayat pengiriman notifikasi.
                </div>
            @endforelse
        </div>
        
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>