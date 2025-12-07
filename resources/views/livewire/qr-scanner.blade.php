@section('title', 'Pemindai Kode QR')

<div class="flex items-center justify-center bg-gray-100 p-4 lg:p-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 w-full max-w-md lg:max-w-2xl overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Pemindai Kode QR Pasien</h2>
                <p class="text-sm text-gray-500 font-medium">Arahkan kamera Anda ke kode QR pasien untuk memulai proses registrasi.</p>
            </div>

            <div id="qr-reader" class="w-full h-auto rounded-lg overflow-hidden border-2 border-dashed border-gray-300"></div>
            
            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                @if ($message)
                    <div class="@if(strpos($message, 'berhasil') !== false) text-green-600 @else text-red-600 @endif font-bold text-sm text-center mb-2 animate-pulse">{{ $message }}</div>
                @endif
                
                @if (!$patient)
                    <div class="text-center text-gray-400 font-medium text-sm">
                        <p>Arahkan kamera ke QR Code untuk melihat detail pasien.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($showPopup && $patient && $jadwal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Pasien Ditemukan!</h3>
            <p class="text-sm text-gray-700 mb-6">Apakah Anda ingin melanjutkan registrasi untuk pasien <b>{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? $this->jadwal->nama_pasien }}</b> ?</p>
            
            <div class="bg-gray-100 p-4 rounded-lg text-left mb-6 text-sm">
                <div class="space-y-2 text-gray-700">
                    <div>
                        <strong class="w-24 text-gray-600 inline-block">Nama:</strong>
                        <span class="font-semibold">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? $this->jadwal->nama_pasien }}</span>
                    </div>
                    <div>
                        <strong class="w-24 text-gray-600 inline-block">No. Identitas:</strong>
                        <span class="font-semibold">{{ $patient->nik_pasien ?? $patient->no_sap }}</span>
                    </div>
                    <div>
                        <strong class="w-24 text-gray-600 inline-block">Jadwal:</strong>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($this->jadwal->tanggal_mcu)->format('d-m-Y') }}</span>
                    </div>
                    <div>
                        <strong class="w-24 text-gray-600 inline-block">Status:</strong>
                        <span class="font-semibold px-2 py-0.5 rounded-full text-xs @if($this->jadwal->status === 'Scheduled') bg-yellow-200 text-yellow-800 @elseif($this->jadwal->status === 'Present') bg-blue-200 text-blue-800 @else bg-green-200 text-green-800 @endif">
                            {{ $this->jadwal->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <button 
                    wire:click="cancelRegistration" 
                    class="px-5 py-2 rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 transition duration-300 text-sm">
                    Batal
                </button>
                <button 
                    wire:click="continueRegistration" 
                    class="px-5 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition duration-300 text-sm">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.pause(); 
            Livewire.dispatch('qrCodeScanned', { uuid: decodedText });
        }

        function onScanFailure(error) {
            console.warn(`Code scan error = ${error}`);
        }

        let html5QrcodeScanner;

        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { fps: 10, qrbox: { width: 300, height: 300 }, aspectRatio: 1.777778 });
            
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        document.addEventListener('livewire:initialized', () => {
             startScanner();
        });

        Livewire.on('qrScanResumed', () => {
            html5QrcodeScanner.resume();
        });
        
    </script>
@endpush