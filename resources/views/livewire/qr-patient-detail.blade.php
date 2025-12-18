@section('title', 'Detail Jadwal Pasien MCU')

{{-- OUTER CONTAINER: Minimal padding untuk mobile (px-2), Max Width untuk desktop (md:max-w-6xl) --}}
<div class="py-4 px-2 md:max-w-6xl md:mx-auto md:px-4"> 
    
    {{-- KARTU UTAMA --}}
    <div class="bg-white rounded-xl shadow-xl p-3 sm:p-4 md:p-8 border border-gray-100">

        {{-- 1. HEADER DAN TOMBOL UNDUH GABUNGAN --}}
        <div class="mb-5 flex flex-col md:flex-row justify-between md:items-center space-y-3 md:space-y-0 border-b md:border-b-0 pb-3 md:pb-0">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Detail Pasien 🧑‍⚕️</h1>
            
            <button
                x-data="{ jadwalId: {{ $jadwal->id }}, mergedUrl: '{{ route('download.mcu.summary', ['jadwalId' => '__ID__']) }}' }"
                @click.prevent="window.open(mergedUrl.replace('__ID__', jadwalId), '_blank')"
                class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 transition ease-in-out duration-150"
            >
                <i class="fas fa-file-pdf mr-2"></i> Lihat Hasil MCU
            </button>
        </div>

        <hr class="mb-6 hidden md:block"> 

        {{-- 2. BAGIAN DETAIL PASIEN (Responsif: 1 kolom di mobile, 2 kolom di desktop) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            {{-- KIRI: KODE PENDAFTARAN (FIXED: items-center di mobile, items-start di desktop) --}}
            <div class="flex flex-col items-center p-3 md:p-6 bg-red-50 rounded-xl border border-red-100 shadow-inner">
                {{-- items-center (mobile) membuat QR di tengah, md:items-start (desktop) membuat QR rata kiri --}}
                <h3 class="text-base font-semibold text-red-700 mb-3 md:mb-4">Kode Pendaftaran</h3>
                <div class="bg-white p-2 rounded-lg shadow-md">
                    @if ($qrCodeImage)
                        <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code Pasien" class="w-32 h-32 md:w-40 md:h-40"> 
                    @else
                        <p class="text-gray-500 font-semibold text-center text-sm">QR Code Tidak Tersedia</p>
                    @endif
                </div>
            </div>

            {{-- KANAN: DATA PRIBADI --}}
            <div class="flex flex-col space-y-3 justify-center"> 
                @if ($patient)
                    <h2 class="text-lg md:text-2xl font-extrabold text-gray-900">{{ $patient->nama_lengkap ?? $patient->nama_karyawan }}</h2>
                    <p class="text-xs md:text-sm text-gray-600 mb-2">{{ $patient->perusahaan_asal ?? 'PT Semen Tonasa' }}</p>

                    {{-- Status Jadwal --}}
                    <div class="flex items-center">
                        <span class="font-bold text-xs md:text-sm text-red-600 mr-3">Status MCU:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($jadwal->status === 'Scheduled') bg-yellow-100 text-yellow-800
                            @elseif($jadwal->status === 'Present') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ $jadwal->status }}
                        </span>
                    </div>

                    {{-- Detail List Pasien --}}
                    <div class="space-y-1.5 text-xs text-gray-700 p-3 bg-gray-50 rounded-lg md:text-sm">
                        
                        <div class="flex justify-between items-center border-b pb-1">
                            <span class="font-semibold text-gray-600 flex-shrink-0 mr-2">SAP / NIK:</span>
                            <span class="text-right truncate">{{ $patient->no_sap ?? 'N/A' }} / {{ $patient->nik_pasien ?? $patient->nik_karyawan }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center border-b pb-1">
                            <span class="font-semibold text-gray-600 flex-shrink-0 mr-2">Tgl. Lahir:</span>
                            <span class="text-right">{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d F Y') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center border-b pb-1">
                            <span class="font-semibold text-gray-600 flex-shrink-0 mr-2">Jenis Kelamin:</span>
                            <span class="text-right">{{ $patient->jenis_kelamin }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-600 flex-shrink-0 mr-2">Paket MCU:</span>
                            <span class="text-right font-medium text-red-600 truncate">{{ $jadwal->paketMcu->nama_paket ?? 'N/A' }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-3 bg-red-100 rounded-lg text-center">
                        <h2 class="text-base font-bold text-red-700 mb-1">Pasien Tidak Ditemukan ⚠️</h2>
                        <p class="text-red-600 text-xs">Pastikan data karyawan/peserta MCU terdaftar.</p>
                    </div>
                @endif
            </div>
        </div>

        <hr class="mb-4">

        {{-- 3. TABS NAVIGASI (FIXED: Menggunakan flex-wrap agar tabs turun ke bawah di mobile) --}}
        <div class="border-b border-gray-200 mb-4 px-1 md:px-0"> {{-- Tambahkan px-1 di sini untuk jarak tepi --}}
            <ul class="flex flex-wrap -mb-px text-xs md:text-sm font-semibold text-center" role="tablist">

                {{-- TAB RINGKASAN --}}
                <li class="mr-1 flex-shrink-0" role="presentation">
                    <button class="inline-block px-2 py-2 md:px-4 md:py-2 border-b-2 rounded-t-lg @if($activeTab === 'summary') text-red-600 border-red-600 @else text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif"
                            wire:click="$set('activeTab', 'summary')"
                            type="button">Ringkasan</button>
                </li>

                {{-- TAB RESUME --}}
                <li class="mr-1 flex-shrink-0" role="presentation">
                    <button class="inline-block px-2 py-2 md:px-4 md:py-2 border-b-2 rounded-t-lg @if($activeTab === 'resume') text-red-600 border-red-600 @else text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif"
                        wire:click="$set('activeTab', 'resume')"
                        type="button">Resume</button> 
                </li>

                {{-- Tabs untuk setiap Poli --}}
                @if($polis->count() > 0)
                    @foreach ($polis as $poli)
                        @php
                            // Pemendekan nama poli untuk layar kecil
                            $shortName = match(strtoupper($poli->nama_poli)) {
                                'RESUME DOKTER' => 'Resume', 'LABORATORIUM' => 'Lab', 'FISIK' => 'Fisik', 'GIGI' => 'Gigi', 'MATA' => 'Mata', 'EKG' => 'EKG', 'AUDIOMETRI' => 'Audio', 'SPIROMETRI' => 'Spiro', 'KEBUGARAN' => 'Bugar', 'THORAX PHOTO' => 'Thorax', 'TREADMILL' => 'Tread', 'USG' => 'USG', default => $poli->nama_poli,
                            };
                            $displayName = $shortName; 
                        @endphp
                        <li class="mr-1 flex-shrink-0" role="presentation">
                            <button class="inline-block px-2 py-2 md:px-4 md:py-2 border-b-2 rounded-t-lg
                                @if($activeTab === 'poli-' . $poli->id) text-red-600 border-red-600
                                @else text-gray-600 hover:text-gray-800 hover:border-gray-300 @endif"
                                wire:click="$set('activeTab', 'poli-{{ $poli->id }}')"
                                type="button">
                                {{ $displayName }}
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>

        {{-- 4. KONTEN TABS (DIBUAT FULL WIDTH DARI KARTU UTAMA) --}}
        <div id="tab-content" class="md:px-0"> 
            
            {{-- TAB RINGKASAN POLI --}}
            @if ($activeTab === 'summary')
                <div class="p-3 md:p-4 bg-gray-50 rounded-lg shadow-inner">
                    <h3 class="text-base font-bold mb-3 text-gray-800">Ringkasan Status Poli</h3>
                    <div class="overflow-x-auto"> 
                        <table class="min-w-full text-xs md:text-sm text-left text-gray-500 rounded-lg"> 
                            <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                                <tr>
                                    <th scope="col" class="py-2 px-3 md:py-3 md:px-4">Poli</th> 
                                    <th scope="col" class="py-2 px-3 md:py-3 md:px-4 text-center">Status</th> 
                                    <th scope="col" class="py-2 px-3 md:py-3 md:px-4 text-center">Aksi</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($polis as $poli)
                                    @php
                                        $poliData = $jadwalPoliData[$poli->id] ?? (object)['status' => 'Pending', 'id' => null];
                                        $shortName = match(strtoupper($poli->nama_poli)) {
                                            'RESUME DOKTER' => 'Resume', 'LABORATORIUM' => 'Lab', 'FISIK' => 'Fisik', 'GIGI' => 'Gigi', 'MATA' => 'Mata', 'EKG' => 'EKG', 'AUDIOMETRI' => 'Audio', 'SPIROMETRI' => 'Spiro', 'KEBUGARAN' => 'Bugar', 'THORAX PHOTO' => 'Thorax', 'TREADMILL' => 'Tread', 'USG' => 'USG', default => $poli->nama_poli,
                                        };
                                    @endphp
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="py-2 px-3 md:py-3 md:px-4 font-medium text-gray-900">{{ $shortName }}</td>
                                        <td class="py-2 px-3 md:py-3 md:px-4 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                                @if($poliData->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @elseif($poliData->status === 'Done') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $poliData->status }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 md:py-3 md:px-4 text-center space-x-1 flex justify-center">
                                            @if($poliData->id)
                                                <button
                                                    wire:click="markAsDone({{ $poliData->id }})"
                                                    class="px-1.5 py-0.5 rounded-md text-white font-semibold text-xs bg-green-500 hover:bg-green-600 transition-colors duration-200 disabled:opacity-50"
                                                    @if($poliData->status === 'Done') disabled @endif
                                                >
                                                    Done
                                                </button>
                                                <button
                                                    wire:click="markAsPending({{ $poliData->id }})"
                                                    class="px-1.5 py-0.5 rounded-md text-gray-700 font-semibold text-xs bg-gray-200 hover:bg-gray-300 transition-colors duration-200 disabled:opacity-50"
                                                    @if($poliData->status === 'Pending') disabled @endif
                                                >
                                                    Cancel
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 italic">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 px-3 text-center text-gray-500 bg-white">Paket MCU tidak memiliki daftar Poli.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{--TAB RESUME (dan tabs poli lainnya, disamakan stylingnya) --}}
            @if ($activeTab === 'resume')
                <div class="p-3 md:p-4 bg-gray-50 rounded-lg shadow-inner">
                    <form wire:submit.prevent="saveResume" class="space-y-4 md:space-y-6">
                        <h3 class="text-base md:text-lg font-bold text-gray-800 border-b pb-2 mb-3">Kesimpulan Pemeriksaan 📝</h3>

                        {{-- BAGIAN INPUT HASIL PEMERIKSAAN --}}
                        <div class="space-y-3 border p-2 md:p-3 rounded-lg bg-white">
                            <h4 class="text-sm font-bold text-red-600">Hasil Pemeriksaan</h4>

                            @php
                            $resumeFields = [
                                'bmi' => ['label' => '1. BMI', 'placeholder' => 'Contoh: 27.63 kg/m2 (Obesitas I)'], 
                                'laboratorium' => ['label' => '2. Lab', 'placeholder' => 'ISK, Peningkatan SGOT, dll.'], 
                                'ecg' => ['label' => '3. ECG/Jantung', 'placeholder' => 'Sinus Rhythm, HR 67x/i.'], 
                                'gigi' => ['label' => '4. Gigi', 'placeholder' => 'OH Buruk'], 
                                'mata' => ['label' => '5. Mata', 'placeholder' => 'Presbiopia'], 
                                'spirometri' => ['label' => '6. Spiro', 'placeholder' => '-'], 
                                'audiometri' => ['label' => '7. Audio', 'placeholder' => 'Normal'], 
                                'kesegaran' => ['label' => '8. Bugar', 'placeholder' => 'sangat kurang'], 
                                'temuan_lain' => ['label' => '9. Temuan lain', 'placeholder' => 'Hipertensi'], 
                                'thorax_photo' => ['label' => '10. Thorax', 'placeholder' => 'Cor, Pulmo dlm bts normal'], 
                                'treadmill' => ['label' => '11. Treadmill', 'placeholder' => 'Negatif/Positif'], 
                                'usg' => ['label' => '12. USG', 'placeholder' => 'Fatty Liver/Normal'],
                            ];
                            @endphp

                            @foreach ($resumeFields as $key => $field)
                                <div>
                                    <label for="resume_{{ $key }}" class="block text-xs font-semibold text-gray-700 mb-0.5">{{ $field['label'] }}</label>
                                    <input type="text" id="resume_{{ $key }}" wire:model.defer="resumeData.{{ $key }}"
                                        class="mt-0.5 block w-full rounded-lg border-gray-300 shadow-sm text-xs md:text-sm focus:border-red-500 focus:ring-red-500" 
                                        placeholder="{{ $field['placeholder'] }}">
                                    @error("resumeData.{$key}") <span class="text-red-500 text-xs mt-0.5 block">{{ $message }}</span> @enderror
                                </div>
                            @endforeach
                        </div>

                        {{-- Input Saran --}}
                        <div>
                            <label for="resume_saran" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">
                                ✅ Saran Dokter
                            </label>
                            <textarea id="resume_saran" wire:model.defer="resumeSaran" rows="4" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-xs md:text-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Contoh: Olahraga rutin, Perbanyak air putih, Pengobatan rutin hipertensi, dll."></textarea>
                            @error('resumeSaran') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Input Kategori dan Tombol Aksi --}}
                        <div class="flex flex-col space-y-2 border-t pt-3"> 

                            {{-- Input Kategori --}}
                            <div>
                                <label for="resume_kategori" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">
                                    ⭐ Kategori Akhir
                                </label>
                                <input type="text" id="resume_kategori" wire:model.defer="resumeKategori"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-xs md:text-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Contoh: Fit With Note (K2)">
                                @error('resumeKategori') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Tombol Simpan dan Lihat PDF --}}
                            <div class="flex flex-col space-y-2 md:flex-row md:space-x-3 md:space-y-0 pt-2">
                                <button type="submit"
                                    class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150"
                                    wire:loading.attr="disabled" wire:target="saveResume">
                                    <span wire:loading.remove wire:target="saveResume"><i class="fas fa-save mr-2"></i> Simpan Resume</span>
                                    <span wire:loading wire:target="saveResume">Menyimpan...</span>
                                </button>
                                <button
                                    x-data="{ jadwalId: {{ $jadwal->id }}, pdfUrl: '{{ route('download.resume.pdf', ['jadwalId' => '__ID__']) }}' }"
                                    @click.prevent="window.open(pdfUrl.replace('__ID__', jadwalId), '_blank')"
                                    class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition ease-in-out duration-150">
                                    <i class="fas fa-file-pdf mr-2"></i> Lihat PDF Resume
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Tabs untuk Setiap Poli (Form Input/Upload) --}}
            @foreach ($polis as $poli)
                @if ($activeTab === 'poli-' . $poli->id)
                    <div class="p-3 md:p-4 bg-gray-50 rounded-lg shadow-inner">
                        <h3 class="text-base md:text-lg font-bold mb-3 text-gray-800">Input Hasil {{ $poli->nama_poli }}</h3>

                        {{-- RENDER KOMPONEN LIVEWIRE --}}
                        @if (strtoupper($poli->nama_poli) === 'GIGI')
                            @livewire('poli-gigi-form', [ 'jadwalId' => $jadwal->id, 'poliData' => $jadwalPoliData[$poli->id] ])
                        
                        @elseif (strtoupper($poli->nama_poli) === 'KEBUGARAN')
                            @livewire('kebugaran-form', [ 'patient' => $patient, 'jadwalPoliId' => $jadwalPoliData[$poli->id]->id, 'poliData' => $jadwalPoliData[$poli->id] ])

                        @elseif (strtoupper($poli->nama_poli) === 'FISIK')
                            @livewire('poli-fisik-form', [ 'patient' => $patient, 'jadwalId' => $jadwalPoliData[$poli->id]->id, 'poliData' => $jadwalPoliData[$poli->id] ])

                        @elseif (in_array(strtoupper($poli->nama_poli), $uploadablePoliNames))
                            <div class="space-y-4">
                                <div class="mb-4">
                                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1">Pilih File PDF Hasil {{ $poli->nama_poli }}</label>
                                    <div class="mt-1 flex flex-col space-y-3">
                                        <div class="flex items-center space-x-3">
                                            <label for="file-{{ $poli->id }}" class="flex-shrink-0 cursor-pointer bg-red-600 hover:bg-red-700 text-white font-bold py-1.5 px-2 text-xs rounded-md shadow-md transition duration-150 ease-in-out">
                                                <span><i class="fas fa-upload mr-1"></i> Pilih File</span>
                                            </label>

                                            <input type="file" id="file-{{ $poli->id }}" wire:model="pdfFiles.{{ $poli->id }}" accept="application/pdf" class="sr-only">

                                            <span class="text-xs md:text-sm text-gray-600 truncate" wire:loading.remove wire:target="pdfFiles.{{ $poli->id }}">
                                                @if(isset($pdfFiles[$poli->id]))
                                                    {{ $pdfFiles[$poli->id]->getClientOriginalName() }}
                                                @elseif(isset($uploadedFileNames[$poli->id]) && $uploadedFileNames[$poli->id])
                                                    {{ $uploadedFileNames[$poli->id] }}
                                                @else
                                                    No file chosen
                                                @endif
                                            </span>
                                            <span class="text-xs md:text-sm text-gray-500" wire:loading wire:target="pdfFiles.{{ $poli->id }}">Mengunggah...</span>
                                        </div>
                                        @error('pdfFiles.' . $poli->id)
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                </div>
                                </div>

                                {{-- Tombol Aksi File --}}
                                <div class="flex flex-col space-y-2 md:flex-row md:space-x-3 md:space-y-0 pt-2 border-t">
                                    <button
                                        wire:click="savePdf({{ $poli->id }})"
                                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out text-xs"
                                        wire:loading.attr="disabled" wire:target="pdfFiles.{{ $poli->id }}">
                                        <span wire:loading.remove wire:target="pdfFiles.{{ $poli->id }}">Simpan File</span>
                                        <span wire:loading wire:target="pdfFiles.{{ $poli->id }}">Mengunggah...</span>
                                    </button>

                                    @if (isset($jadwalPoliData[$poli->id]) && $jadwalPoliData[$poli->id]->file_path)
                                        <a href="{{ route('download', ['filePath' => basename($jadwalPoliData[$poli->id]->file_path)]) }}" target="_blank" class="w-full md:w-auto text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out text-xs">
                                            <i class="fas fa-eye"></i> Lihat File
                                        </a>
                                        <a href="{{ route('download', ['filePath' => basename($jadwalPoliData[$poli->id]->file_path)]) }}" download class="w-full md:w-auto text-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out text-xs">
                                            <i class="fas fa-download"></i> Unduh File
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-white rounded-lg border border-gray-200">
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-info-circle mr-2 text-red-500"></i> Poli ini adalah poli interaktif dan memerlukan input di halaman ini.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>

{{-- <style>
/* Chrome, Safari, Opera: Sembunyikan scrollbar pada elemen tab */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* IE and Edge */
.scrollbar-hide {
    -ms-overflow-style: none;
}
</style> --}}