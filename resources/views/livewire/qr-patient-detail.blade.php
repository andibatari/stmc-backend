@section('title', 'Detail Jadwal Pasien MCU')


<div>
    <div class="max-w-6xl mx-auto py-8">
        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10 border border-gray-100">

            {{-- HEADER DAN TOMBOL UNDUH GABUNGAN --}}
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Detail Pasien</h1>

                {{-- Tombol Unduh Semua PDF --}}
                <button 
                    x-data="{ jadwalId: {{ $jadwal->id }}, mergedUrl: '{{ route('download.mcu.summary', ['jadwalId' => '__ID__']) }}' }"
                    @click.prevent="window.open(mergedUrl.replace('__ID__', jadwalId), '_blank')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    <i class="fas fa-download mr-2"></i> Lihat Hasil Pemeriksaan
                </button>
            </div>
            
            {{-- Bagian Atas: QR Code dan Data Pasien --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {{-- Kolom Kiri: QR Code --}}
                <div class="flex items-center justify-center p-4 bg-gray-50 rounded-lg h-64">
                    @if ($qrCodeImage)
                        <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code Pasien" class="max-h-full">
                    @else
                        <p class="text-gray-500 font-semibold">QR Code Tidak Tersedia</p>
                    @endif
                </div>

                {{-- Kolom Kanan: Data Pribadi Pasien --}}
                <div class="flex flex-col justify-center">
                    @if ($patient)
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $patient->nama_lengkap ?? $patient->nama_karyawan }}</h2>
                        <p class="text-lg text-gray-600 mb-4">{{ $patient->perusahaan_asal ?? 'PT Semen Tonasa' }}</p>

                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold w-24">No. SAP:</span>
                                <span>{{ $patient->no_sap ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold w-24">NIK:</span>
                                <span>{{ $patient->nik_pasien ?? $patient->nik_karyawan }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold w-24">Tgl. Lahir:</span>
                                <span>{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d F Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold w-24">Jenis Kelamin:</span>
                                <span>{{ $patient->jenis_kelamin }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold w-24">Paket:</span>
                                <span>{{ $jadwal->paketMcu->nama_paket ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center ">
                                <span class="font-semibold w-24">Status Jadwal:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold @if($jadwal->status === 'Scheduled') bg-yellow-200 text-yellow-800 @elseif($jadwal->status === 'Present') bg-blue-200 text-blue-800 @else bg-green-200 text-green-800 @endif">
                                    {{ $jadwal->status }}
                                </span>
                            </div>
                        </div>
                    @else
                        <h2 class="text-2xl font-bold text-red-500 mb-2">Pasien Tidak Ditemukan</h2>
                        <p class="text-gray-600">Pastikan data karyawan/peserta MCU terdaftar dan relasi berfungsi.</p>
                        <div class="space-y-2 text-sm text-gray-700 mt-4">
                            <div class="flex items-center space-x-2"><span class="font-semibold w-24">No. SAP:</span><span>N/A</span></div>
                            <div class="flex items-center space-x-2"><span class="font-semibold w-24">NIK:</span><span>N/A</span></div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bagian Bawah: Tabs Navigasi --}}
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                    
                    {{-- TAB RINGKASAN POLI --}}
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg @if($activeTab === 'summary') text-red-600 border-red-600 @else hover:text-gray-600 hover:border-gray-300 @endif"
                                wire:click="$set('activeTab', 'summary')"
                                type="button">RINGKASAN</button>
                    </li>

                    {{-- TAB RESUME BARU --}}
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg @if($activeTab === 'resume') text-red-600 border-red-600 @else hover:text-gray-600 hover:border-gray-300 @endif"
                            wire:click="$set('activeTab', 'resume')"
                            type="button">RESUME DOKTER</button>
                    </li>
                    {{-- Tabs untuk setiap Poli --}}
                    @if($polis->count() > 0)
                        @foreach ($polis as $poli)
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg 
                                    @if($activeTab === 'poli-' . $poli->id) text-red-600 border-red-600 
                                    @else hover:text-gray-600 hover:border-gray-300 @endif"
                                    wire:click="$set('activeTab', 'poli-{{ $poli->id }}')"
                                    type="button">
                                    {{ $poli->nama_poli }}
                                </button>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- Konten Tabs --}}
            <div id="tab-content">

                {{-- TAB RINGKASAN POLI --}}
                @if ($activeTab === 'summary')
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-xl font-bold mb-4">Ringkasan Status Poli</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 rounded-lg overflow-hidden">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">Poli</th>
                                        <th scope="col" class="py-3 px-6 text-center">Status Kehadiran</th>
                                        <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($polis as $poli)
                                        @php
                                            $poliData = $jadwalPoliData[$poli->id] ?? (object)['status' => 'Pending'];
                                        @endphp
                                        <tr class="bg-white border-b">
                                            <td class="py-4 px-6 font-medium text-gray-900">{{ $poli->nama_poli }}</td>
                                            <td class="py-4 px-6 text-center">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                    @if($poliData->status === 'Pending') bg-yellow-200 text-yellow-800
                                                    @elseif($poliData->status === 'Done') bg-green-200 text-green-800
                                                    @else bg-red-200 text-red-800 @endif">
                                                    {{ $poliData->status }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-center space-x-2">
                                                <button 
                                                    wire:click="markAsDone({{ $poli->id }})"
                                                    class="px-4 py-1 rounded-md text-white font-semibold text-xs bg-green-500 hover:bg-green-600 transition-colors duration-200"
                                                >
                                                    Done
                                                </button>
                                                <button 
                                                    wire:click="markAsPending({{ $poli->id }})"
                                                    class="px-4 py-1 rounded-md text-gray-700 font-semibold text-xs bg-gray-200 hover:bg-gray-300 transition-colors duration-200"
                                                >
                                                    Cancel
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 px-6 text-center text-gray-500">Paket MCU tidak memiliki daftar Poli.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{--TAB RESUME --}}
                @if ($activeTab === 'resume')
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <form wire:submit.prevent="saveResume" class="space-y-6">
                            <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Kesimpulan Pemeriksaan Medis</h3>

                            {{-- BAGIAN BARU: INPUT FIELD UNTUK BUTIR 1 SAMPAI 9 --}}
                            <div class="space-y-4 border p-4 rounded-lg bg-white">
                                <h4 class="text-lg font-semibold text-gray-700">Hasil Pemeriksaan</h4>

                                @php
                                $resumeFields = [
                                    'bmi' => ['label' => '1. BMI', 'placeholder' => 'Contoh: 27.63 kg/m2 (Obesitas I)'],
                                    'laboratorium' => ['label' => '2. Hasil Laboratorium', 'placeholder' => 'Contoh: ISK, Peningkatan SGOT, dll.'],
                                    'ecg' => ['label' => '3. Hasil Pemeriksaan ECG/Jantung', 'placeholder' => 'Contoh: Sinus Rhythm, HR 67x/i, Normo axis.'],
                                    'gigi' => ['label' => '4. Hasil Pemeriksaan Gigi', 'placeholder' => 'Contoh: OH Buruk'],
                                    'mata' => ['label' => '5. Hasil Pemeriksaan Mata', 'placeholder' => 'Contoh: Presbiopia'],
                                    'spirometri' => ['label' => '6. Hasil Pemeriksaan Spirometri', 'placeholder' => 'Contoh: -'],
                                    'audiometri' => ['label' => '7. Hasil Pemeriksaan Audiometri', 'placeholder' => 'Contoh: Dalam Batas Normal'],
                                    'kesegaran' => ['label' => '8. Hasil Pemeriksaan Kesegaran Jasmani', 'placeholder' => 'Contoh: sangat kurang'],
                                    'temuan_lain' => ['label' => '9. Temuan lain', 'placeholder' => 'Contoh: Hipertensi'],
                                    'thorax_photo' => ['label' => '10. Thorax Photo', 'placeholder' => 'Contoh: Cor, Pulmo dalam batas normal'],
                                    'treadmill' => ['label' => '11. Treadmill', 'placeholder' => 'Contoh: Negatif/Positif'],
                                    'usg' => ['label' => '12. USG', 'placeholder' => 'Contoh: Fatty Liver/Normal'],
                                ];
                                @endphp
                                
                                @foreach ($resumeFields as $key => $field)
                                    <div>
                                        <label for="resume_{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $field['label'] }}</label>
                                        <input type="text" id="resume_{{ $key }}" wire:model.defer="resumeData.{{ $key }}" 
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                                            placeholder="{{ $field['placeholder'] }}">
                                        @error("resumeData.{$key}") <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endforeach
                            </div>
                            {{-- AKHIR INPUT BUTIR 1 SAMPAI 9 --}}


                            {{-- Input Saran --}}
                            <div>
                                <label for="resume_saran" class="block text-sm font-semibold text-gray-700 mb-1">
                                    ✅ Saran Dokter (Per Poin di PDF)
                                </label>
                                <textarea id="resume_saran" wire:model.defer="resumeSaran" rows="7" 
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Contoh: Olahraga rutin minimal 3x seminggu, Perbanyak konsumsi air putih, Pengobatan rutin hipertensi, dll."></textarea>
                                @error('resumeSaran') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Input Kategori dan Tombol Aksi --}}
                            <div class="flex justify-between items-end border-t pt-4">
                                
                                {{-- Input Kategori --}}
                                <div>
                                    <label for="resume_kategori" class="block text-sm font-semibold text-gray-700 mb-1">
                                        ⭐ Kategori Hasil Akhir
                                    </label>
                                    <input type="text" id="resume_kategori" wire:model.defer="resumeKategori" 
                                        class="mt-1 block w-64 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Contoh: Fit With Note (K2)">
                                    @error('resumeKategori') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- Tombol Simpan dan Lihat PDF --}}
                                <div class="space-x-4">
                                    
                                    {{-- Tombol Simpan --}}
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150"
                                        wire:loading.attr="disabled"
                                        wire:target="saveResume"
                                    >
                                        <span wire:loading.remove wire:target="saveResume"><i class="fas fa-save mr-2"></i> Simpan Resume</span>
                                        <span wire:loading wire:target="saveResume">Menyimpan...</span>
                                    </button>
                                    
                                    {{-- Tombol Lihat PDF Resume --}}
                                    <button 
                                        x-data="{ jadwalId: {{ $jadwal->id }}, pdfUrl: '{{ route('download.resume.pdf', ['jadwalId' => '__ID__']) }}' }"
                                        @click.prevent="window.open(pdfUrl.replace('__ID__', jadwalId), '_blank')"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition ease-in-out duration-150"
                                    >
                                        <i class="fas fa-file-pdf mr-2"></i> Lihat PDF Resume
                                    </button>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                
                {{-- Tabs untuk Setiap Poli --}}
                @foreach ($polis as $poli)
                    @if ($activeTab === 'poli-' . $poli->id)
                        {{-- DIV INI ADALAH CONTAINER UNTUK KONTEN TAB --}}
                        <div class="p-4 bg-gray-50 rounded-lg"> 
                            <h3 class="text-xl font-bold mb-4">Input Hasil {{ $poli->nama_poli }}</h3>
                            
                            {{-- Cek apakah poli adalah Poli Gigi --}}
                            @if (strtoupper($poli->nama_poli) === 'GIGI')
                                {{-- RENDER KOMPONEN LIVEWIRE POLI GIGI DI SINI --}}
                                @livewire('poli-gigi-form', [
                                    'jadwalId' => $jadwal->id, 
                                    'poliData' => $jadwalPoliData[$poli->id]
                                ])
                            
                            {{-- Tambahan untuk Poli Kebugaran --}}
                            @elseif (strtoupper($poli->nama_poli) === 'KEBUGARAN')
                                
                                {{-- RENDER KOMPONEN LIVEWIRE KEBUGARAN DI SINI --}}
                                @livewire('kebugaran-form', [ 
                                    'patient' => $patient, // <-- TAMBAHKAN KEMBALI BARIS INI
                                    'jadwalPoliId' => $jadwalPoliData[$poli->id]->id, 
                                    'poliData' => $jadwalPoliData[$poli->id] 
                                ])

                            {{-- Tambahan untuk Poli Fisik --}}
                            @elseif (strtoupper($poli->nama_poli) === 'FISIK')
                                {{-- RENDER KOMPONEN LIVEWIRE FISIK DI SINI --}}
                                @livewire('poli-fisik-form', [ 
                                    'patient' => $patient, // <-- TAMBAHKAN KEMBALI BARIS INI
                                    'jadwalId' => $jadwalPoliData[$poli->id]->id, 
                                    'poliData' => $jadwalPoliData[$poli->id] 
                                ])
                                
                            @elseif (in_array(strtoupper($poli->nama_poli), $uploadablePoliNames))
                                <div class="space-y-4">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Pilih File PDF</label>
                                        <div class="mt-1 flex items-center space-x-3">
                                            <label for="file-{{ $poli->id }}" class="cursor-pointer bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out">
                                                <span>Choose File</span>
                                            </label>
                                            
                                            <input 
                                                type="file" 
                                                id="file-{{ $poli->id }}" 
                                                wire:model="pdfFiles.{{ $poli->id }}" 
                                                accept="application/pdf"
                                                class="sr-only"
                                            >
                                            
                                            {{-- Tampilan nama file atau "No file chosen" --}}
                                            <span class="text-sm text-gray-500" wire:loading.remove wire:target="pdfFiles.{{ $poli->id }}">
                                                @if(isset($pdfFiles[$poli->id]))
                                                    {{ $pdfFiles[$poli->id]->getClientOriginalName() }}
                                                @elseif(isset($uploadedFileNames[$poli->id]) && $uploadedFileNames[$poli->id])
                                                    {{ $uploadedFileNames[$poli->id] }}
                                                @else
                                                    No file chosen
                                                @endif
                                            </span>
                                            
                                            {{-- Indikator loading saat file sedang dipilih --}}
                                            <span class="text-sm text-gray-500" wire:loading wire:target="pdfFiles.{{ $poli->id }}">
                                                Membaca file...
                                            </span>

                                            @error('pdfFiles.' . $poli->id) 
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4">
                                        <button 
                                            wire:click="savePdf({{ $poli->id }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out"
                                            wire:loading.attr="disabled"
                                            wire:target="pdfFiles.{{ $poli->id }}"
                                        >
                                            <span wire:loading.remove wire:target="pdfFiles.{{ $poli->id }}">Upload File</span>
                                            <span wire:loading wire:target="pdfFiles.{{ $poli->id }}">Mengunggah...</span>
                                        </button>
                                        
                                        @if (isset($jadwalPoliData[$poli->id]) && $jadwalPoliData[$poli->id]->file_path)
                                            <a href="{{ route('download', ['filePath' => basename($jadwalPoliData[$poli->id]->file_path)]) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out">
                                                <i class="fas fa-eye"></i> Lihat File
                                            </a>
                                            <a href="{{ route('download', ['filePath' => basename($jadwalPoliData[$poli->id]->file_path)]) }}" download class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out">
                                                <i class="fas fa-download"></i> Unduh File
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500">
                                    Poli ini tidak memerlukan unggahan file PDF.
                                </p>
                            @endif
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>
</div>
