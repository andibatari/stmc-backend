<div class="space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-6">
        
        {{-- Bagian Pemilihan Dokter --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Dokter Pemeriksa</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700">Pilih Dokter</label>
                <select wire:model.defer="dokterId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Pilih Dokter --</option>
                    @isset($listDokter)
                        @foreach ($listDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                        @endforeach
                    @endisset
                </select>
                @error('dokterId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 1. TANDA VITAL DAN BMI --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">1. TANDA VITAL & ANTROPOMETRI</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                
                {{-- Tinggi Badan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tinggi Badan (cm)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.tinggi_badan" min="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dataFisik.tanda_vital.tinggi_badan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                {{-- Berat Badan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.berat_badan" min="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dataFisik.tanda_vital.berat_badan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Tekanan Darah (Sistol) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tekanan Darah (Sistol)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.tekanan_darah_sistol" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                {{-- Tekanan Darah (Diastol) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tekanan Darah (Diastol)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.tekanan_darah_diastol" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                {{-- Nadi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nadi (x/mnt)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.nadi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                {{-- Pernafasan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pernafasan (x/mnt)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.pernafasan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                {{-- Suhu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Suhu (°C)</label>
                    <input type="number" step="0.1" wire:model.defer="dataFisik.tanda_vital.suhu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                {{-- SpO2 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">SpO2 (%)</label>
                    <input type="number" wire:model.defer="dataFisik.tanda_vital.spo2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                {{-- Tampilan BMI --}}
                <div class="col-span-4 mt-2">
                    <p class="text-sm font-semibold text-blue-700">BMI: {{ $bmiData['bmi'] }} (Kategori: {{ $bmiData['kategori'] }})</p>
                </div>
            </div>
        </div>

        {{-- 2. KEPALA DAN LEHER --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">2. PEMERIKSAAN KEPALA & LEHER</h3>
            <div class="grid grid-cols-2 gap-4">
                
                {{-- Kepala --}}
                <div>
                    <p class="font-semibold text-sm mb-2">Kepala</p>
                    @foreach (['anemi' => 'Anemi', 'ikterus' => 'Ikterus', 'dyspnoe' => 'Dyspnoe', 'cyanosis' => 'Cyanosis', 'refleks_pupil' => 'Refleks Pupil'] as $key => $label)
                        <div class="mb-2">
                            <label class="block text-xs text-gray-500">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.kepala.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                @if($key === 'tonsil_kanan' || $key === 'tonsil_kiri')
                                    <option value="T1">T1</option>
                                    <option value="T2">T2</option>
                                    <option value="T3">T3</option>
                                @else
                                    <option value="Tidak">Tidak</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Normal">Normal</option>
                                @endif
                                @if($key === 'refleks_pupil')
                                    <option value="Positif">Positif</option>
                                    <option value="Negatif">Negatif</option>
                                @endif
                                @if($key !== 'refleks_pupil')
                                    <option value="Dalam batas normal">Dalam batas normal</option>
                                @endif
                            </select>
                        </div>
                    @endforeach
                    
                    {{-- Telinga (Khusus) --}}
                    <p class="font-semibold text-sm mb-2 mt-4">Telinga & Membran Timpani</p>
                    @foreach (['serumen' => 'Serumen', 'membran_timpani' => 'Membran Timpani'] as $key => $label)
                        <div class="mb-2">
                            <label class="block text-xs text-gray-500">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.kepala.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="Tidak Ada">Tidak Ada</option>
                                <option value="Ada">Ada</option>
                                <option value="Normal">Normal</option>
                                <option value="Dalam batas normal">Dalam batas normal</option>
                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- Leher --}}
                <div>
                    <p class="font-semibold text-sm mb-2">Leher</p>
                    @foreach (['jvp' => 'JVP', 'tiroid' => 'Tiroid', 'kelenjar_getah_bening' => 'KGB'] as $key => $label)
                        <div class="mb-2">
                            <label class="block text-xs text-gray-500">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.leher.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="Dalam batas normal">Dalam batas normal</option>
                                <option value="Pembesaran">Pembesaran</option>
                                <option value="Tidak ada">Tidak ada</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">3. PEMERIKSAAN MATA</h3>
            <div class="grid grid-cols-2 gap-4">
                {{-- Visus --}}
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Visus Kanan</label>
                    <input type="text" wire:model.defer="dataFisik.mata.visus_kanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dataFisik.mata.visus_kanan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Visus Kiri</label>
                    <input type="text" wire:model.defer="dataFisik.mata.visus_kiri" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dataFisik.mata.visus_kiri') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                {{-- Konjungtiva & Sklera --}}
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Konjungtiva</label>
                    <input type="text" wire:model.defer="dataFisik.mata.konjungtiva" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dataFisik.mata.konjungtiva') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Sklera</label>
                    <input type="text" wire:model.defer="dataFisik.mata.sklera" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('dataFisik.mata.sklera') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                {{-- KRITIS: Kesimpulan Mata --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Kesimpulan</label>
                    <textarea wire:model.defer="dataFisik.mata.kesimpulan_mata" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    @error('dataFisik.mata.kesimpulan_mata') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- 3. DADA, PARU, ABDOMEN, EKSTREMITAS --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">4. DADA, PARU, ABDOMEN, EKSTREMITAS</h3>
            
            {{-- DADA --}}
            <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">Dada (Jantung)</p>
            <div class="grid grid-cols-2 gap-4">
                @foreach (['bunyi_jantung_1' => 'Bunyi Jantung I', 'bunyi_jantung_2' => 'Bunyi Jantung II'] as $key => $label)
                    <div class="mb-2">
                        <label class="block text-xs text-gray-500">{{ $label }}</label>
                        <select wire:model.defer="dataFisik.dada.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="Murni">Murni</option>
                            <option value="Reguler">Reguler</option>
                            <option value="Tidak Murni">Tidak Murni</option>
                            <option value="Ireguler">Ireguler</option>
                        </select>
                    </div>
                @endforeach
            </div>

            {{-- PARU --}}
            <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">Paru</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-2">
                    <label class="block text-xs text-gray-500">Bunyi Nafas Dasar</label>
                    <select wire:model.defer="dataFisik.paru.bunyi_nafas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="Vesikular">Vesikular</option>
                        <option value="Bronkial">Bronkial</option>
                        <option value="Bronkovesikular">Bronkovesikular</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="block text-xs text-gray-500">Bunyi Nafas Tambahan</label>
                    <select wire:model.defer="dataFisik.paru.bunyi_nafas_tambahan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="Tidak ada">Tidak ada</option>
                        <option value="Ronkhi">Ronkhi</option>
                        <option value="Wheezing">Wheezing</option>
                        <option value="Stridor">Stridor</option>
                    </select>
                </div>
            </div>

            {{-- ABDOMEN --}}
            <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">Abdomen, Hati & Limpa</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach (['peristaltik' => 'Peristaltik', 'nyeri_tekan' => 'Nyeri Tekan', 'massa' => 'Massa', 'hati' => 'Hati', 'limpa' => 'Limpa'] as $key => $label)
                    <div class="mb-2 col-span-1">
                        <label class="block text-xs text-gray-500">{{ $label }}</label>
                        <select wire:model.defer="dataFisik.abdomen.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="Dalam batas normal">Dalam batas normal</option>
                            <option value="Tidak Ada">Tidak Ada</option>
                            <option value="Ada/Pembesaran">Ada/Pembesaran</option>
                        </select>
                    </div>
                @endforeach
            </div>

            {{-- EKSTREMITAS --}}
            <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">Ekstremitas & Refleks</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="col-span-4 mb-2">
                    <label class="block text-xs text-gray-500">Ekstremitas</label>
                    <select wire:model.defer="dataFisik.ekstremitas.ekstremitas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="Dalam Batas Normal">Dalam Batas Normal</option>
                        <option value="Edema Kanan">Edema Kanan</option>
                        <option value="Edema Kiri">Edema Kiri</option>
                        <option value="Kelainan Lain">Kelainan Lain</option>
                    </select>
                </div>

                {{-- Refleks Fisiologis --}}
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500">Refleks Fisiologis Kanan</label>
                    <select wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="+2">+2 (Normal)</option>
                        <option value="+1">+1 (Menurun)</option>
                        <option value="+3">+3 (Meningkat)</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500">Refleks Fisiologis Kiri</label>
                    <select wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kiri" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="+2">+2 (Normal)</option>
                        <option value="+1">+1 (Menurun)</option>
                        <option value="+3">+3 (Meningkat)</option>
                    </select>
                </div>

                {{-- Refleks Patologis --}}
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500">Refleks Patologis Kanan</label>
                    <select wire:model.defer="dataFisik.ekstremitas.refleks_patologis_kanan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="Tidak Ada">Tidak Ada</option>
                        <option value="Ada">Ada</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500">Refleks Patologis Kiri</label>
                    <select wire:model.defer="dataFisik.ekstremitas.refleks_patologis_kiri" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="Tidak Ada">Tidak Ada</option>
                        <option value="Ada">Ada</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 4. RIWAYAT PAJANAN PEKERJAAN --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">4. RIWAYAT PAJANAN PEKERJAAN</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- FISIK --}}
                <div>
                    <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">A. FISIK</p>
                    @foreach ([
                        'kebisingan' => 'Kebisingan', 'suhu_panas' => 'Suhu panas', 'suhu_dingin' => 'Suhu dingin',
                        'radiasi_non_pengion' => 'Radiasi bukan pengion (Gel mikro, infrared, dll)', 
                        'radiasi_pengion' => 'Radiasi pengion', 'getaran_lokal' => 'Getaran lokal',
                        'getaran_seluruh_tubuh' => 'Getaran seluruh tubuh', 'ketinggian' => 'Ketinggian',
                        'lain_fisik' => 'Lain - lain'
                    ] as $key => $label)
                        <div class="mb-2 flex justify-between items-center">
                            <label class="block text-xs text-gray-600 w-3/4">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.pajanan.fisik.{{ $key }}" class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                    @endforeach
                </div>
                
                {{-- KIMIA --}}
                <div>
                    <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">B. KIMIA</p>
                    @foreach ([
                        'debu_anorganik' => 'Debu anorganik (Silika, semen, dll)', 'debu_organic' => 'Debu organic (Kapas, tekstil, gandum)',
                        'asap' => 'Asap', 'logam_berat' => 'Logam berat (Timah hitam, Air raksa, dll)',
                        'iritan_asam' => 'Iritan asam (Air keras, Asam sulfat)', 'iritan_basa' => 'Iritan basa (Amoniak, Soda api)',
                        'cairan_pembersih' => 'Cairan pembersih (Amonia, Klor, Kporit)', 'pestisida' => 'Pestisida',
                        'uap_logam' => 'Uap logam (Mangan, Seng)', 'lain_kimia' => 'Lain - lain'
                    ] as $key => $label)
                        <div class="mb-2 flex justify-between items-center">
                            <label class="block text-xs text-gray-600 w-3/4">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.pajanan.kimia.{{ $key }}" class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- BIOLOGI --}}
                <div>
                    <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">C. BIOLOGI</p>
                    @foreach ([
                        'bakteri' => 'Bakteri / Virus / Jamur / Parasit', 'darah' => 'Darah / Cairan tubuh lain', 
                        'nyamuk' => 'Nyamuk / Serangga / Lain - lain', 'limbah' => 'Limbah (Kotoran manusia / Hewan)',
                        'lain_biologi' => 'Lain - lain'
                    ] as $key => $label)
                        <div class="mb-2 flex justify-between items-center">
                            <label class="block text-xs text-gray-600 w-3/4">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.pajanan.biologi.{{ $key }}" class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- PSIKOLOGI --}}
                <div>
                    <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">D. PSIKOLOGI</p>
                    @foreach ([
                        'beban_kerja' => 'Beban kerja tidak sesuai dengan waktu dan jumlah pekerjaan', 
                        'pekerjaan_tidak_sesuai' => 'Pekerjaan tidak sesuai dengan pengetahuan dan keterampilan', 
                        'ketidakjelasan_tugas' => 'Ketidakjelasan tugas', 'hambatan_jenjang_karir' => 'Hambatan jenjang karir',
                        'bekerja_giliran' => 'Bekerja giliran (Shift)', 'konflik_teman_sekerja' => 'Konflik dengan teman sekerja',
                        'konflik_keluarga' => 'Konflik dalam keluarga', 'lain_psikologi' => 'Lain - lain'
                    ] as $key => $label)
                        <div class="mb-2 flex justify-between items-center">
                            <label class="block text-xs text-gray-600 w-3/4">{{ $label }}</label>
                            <select wire:model.defer="dataFisik.pajanan.psikologi.{{ $key }}" class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- ERGONOMIS --}}
                <div class="md:col-span-2">
                    <p class="font-semibold text-sm mb-2 mt-4 text-blue-800">E. ERGONOMIS</p>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ([
                            'gerakan_berulang' => 'Gerakan berulang dengan tangan', 'angkat_berat' => 'Angkat / Angkut berat',
                            'duduk_lama' => 'Duduk lama > 4 jam terus - menerus', 'berdiri_lama' => 'Berdiri lama > 4 jam terus - menerus',
                            'posisi_tidak_ergonomis' => 'Posisi tubuh tidak ergonomis', 'pencahayaan_tidak_sesuai' => 'Pencahayaan tidak sesuai',
                            'bekerja_layar_monitor' => 'Bekerja dengan layar / monitor > 4 jam dalam sehari', 'lain_ergonomis' => 'Lain - lain'
                        ] as $key => $label)
                            <div class="mb-2 flex justify-between items-center col-span-1">
                                <label class="block text-xs text-gray-600 w-3/4">{{ $label }}</label>
                                <select wire:model.defer="dataFisik.pajanan.ergonomis.{{ $key }}" class="mt-1 block w-1/4 rounded-md border-gray-300 shadow-sm text-sm">
                                    <option value="Tidak">Tidak</option>
                                    <option value="Ya">Ya</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>


        {{-- 5. KESIMPULAN & KETERANGAN --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">5. DIAGNOSA & KESIMPULAN</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Kesimpulan (Ringkasan Hasil)</label>
                <textarea wire:model.defer="kesimpulan" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                @error('kesimpulan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Keterangan / Saran Tindak Lanjut</label>
                <textarea wire:model.defer="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                @error('keterangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-end space-x-4 pt-4">
            {{-- Tombol Lihat File PDF --}}
            @if($fisikResult->file_path)
                <a href="{{ route('pdf.fisik.view', ['id' => $poliData->id]) }}" target="_blank"
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-all duration-200">
                    Lihat Laporan PDF
                </a>
            @endif
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-all duration-200">
                <span wire:loading.remove wire:target="simpanHasil">Simpan Hasil Pemeriksaan Fisik</span>
                <span wire:loading wire:target="simpanHasil">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
