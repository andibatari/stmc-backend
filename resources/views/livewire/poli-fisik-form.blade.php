<div class="space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-6">
        
        {{-- DOKTER PEMERIKSA --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-user-md text-blue-500 mr-2"></i>Dokter Pemeriksa</h3>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Dokter</label>
                <select wire:model.defer="dokterId" class="block w-full md:w-1/2 rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors">
                    <option value="">-- Pilih Dokter Pemeriksa Fisik --</option>
                    @isset($listDokter)
                        @foreach ($listDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                        @endforeach
                    @endisset
                </select>
                @error('dokterId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 1. TANDA VITAL & ANTROPOMETRI --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">1. Tanda Vital & Antropometri</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi Badan <span class="text-slate-400 lowercase normal-case">(cm)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.tinggi_badan" min="10" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                        @error('dataFisik.tanda_vital.tinggi_badan') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Berat Badan <span class="text-slate-400 lowercase normal-case">(kg)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.berat_badan" min="5" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                        @error('dataFisik.tanda_vital.berat_badan') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sistol <span class="text-slate-400 lowercase normal-case">(mmHg)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.tekanan_darah_sistol" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Diastol <span class="text-slate-400 lowercase normal-case">(mmHg)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.tekanan_darah_diastol" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nadi <span class="text-slate-400 lowercase normal-case">(x/mnt)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.nadi" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nafas <span class="text-slate-400 lowercase normal-case">(x/mnt)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.pernafasan" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Suhu <span class="text-slate-400 lowercase normal-case">(°C)</span></label>
                        <input type="number" step="0.1" wire:model.defer="dataFisik.tanda_vital.suhu" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">SpO2 <span class="text-slate-400 lowercase normal-case">(%)</span></label>
                        <input type="number" wire:model.defer="dataFisik.tanda_vital.spo2" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Tampilan BMI Otomatis --}}
                <div class="mt-6 bg-blue-50 border border-blue-200 p-4 rounded-xl flex items-center justify-between">
                    <span class="text-sm font-bold text-blue-900 uppercase">Kalkulasi BMI Pasien</span>
                    <span class="text-sm font-black text-blue-700 bg-white px-4 py-1.5 rounded-lg border border-blue-200 shadow-sm">
                        {{ $bmiData['bmi'] }} - {{ $bmiData['kategori'] }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 2. KEPALA DAN LEHER --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">2. Kepala & Leher</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Kepala --}}
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                    <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Area Kepala</h4>
                    <div class="space-y-4">
                        @foreach (['anemi' => 'Anemi', 'ikterus' => 'Ikterus', 'dyspnoe' => 'Dyspnoe', 'cyanosis' => 'Cyanosis', 'refleks_pupil' => 'Refleks Pupil'] as $key => $label)
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-bold text-slate-500 w-1/2">{{ $label }}</label>
                                <select wire:model.defer="dataFisik.kepala.{{ $key }}" class="block w-1/2 rounded-lg border-slate-200 bg-white text-xs font-medium focus:border-blue-500 focus:ring-blue-500 cursor-pointer py-1.5">
                                    @if($key === 'tonsil_kanan' || $key === 'tonsil_kiri')
                                        <option value="T1">T1</option><option value="T2">T2</option><option value="T3">T3</option>
                                    @else
                                        <option value="Tidak">Tidak</option><option value="Ya">Ya</option><option value="Normal">Normal</option>
                                    @endif
                                    @if($key === 'refleks_pupil')
                                        <option value="Positif">Positif</option><option value="Negatif">Negatif</option>
                                    @endif
                                    @if($key !== 'refleks_pupil')
                                        <option value="Dalam batas normal">Dalam batas normal</option>
                                    @endif
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Telinga & Leher --}}
                <div class="flex flex-col space-y-6">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Telinga</h4>
                        <div class="space-y-4">
                            @foreach (['serumen' => 'Serumen', 'membran_timpani' => 'Membran Timpani'] as $key => $label)
                                <div class="flex justify-between items-center">
                                    <label class="text-xs font-bold text-slate-500 w-1/2">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.kepala.{{ $key }}" class="block w-1/2 rounded-lg border-slate-200 bg-white text-xs font-medium focus:border-blue-500 focus:ring-blue-500 cursor-pointer py-1.5">
                                        <option value="Tidak Ada">Tidak Ada</option><option value="Ada">Ada</option>
                                        <option value="Normal">Normal</option><option value="Dalam batas normal">Dalam batas normal</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Leher</h4>
                        <div class="space-y-4">
                            @foreach (['jvp' => 'JVP', 'tiroid' => 'Tiroid', 'kelenjar_getah_bening' => 'KGB'] as $key => $label)
                                <div class="flex justify-between items-center">
                                    <label class="text-xs font-bold text-slate-500 w-1/2">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.leher.{{ $key }}" class="block w-1/2 rounded-lg border-slate-200 bg-white text-xs font-medium focus:border-blue-500 focus:ring-blue-500 cursor-pointer py-1.5">
                                        <option value="Dalam batas normal">Dalam batas normal</option>
                                        <option value="Pembesaran">Pembesaran</option>
                                        <option value="Tidak ada">Tidak ada</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. DADA, PARU, ABDOMEN --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">4. Dada, Paru, Abdomen & Ekstremitas</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Dada & Paru --}}
                <div class="space-y-6">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Dada (Jantung)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach (['bunyi_jantung_1' => 'Bunyi I', 'bunyi_jantung_2' => 'Bunyi II'] as $key => $label)
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-2">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.dada.{{ $key }}" class="block w-full rounded-lg border-slate-200 bg-white text-xs font-medium focus:border-blue-500 focus:ring-blue-500 py-2 cursor-pointer">
                                        <option value="Murni">Murni</option><option value="Reguler">Reguler</option>
                                        <option value="Tidak Murni">Tidak Murni</option><option value="Ireguler">Ireguler</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Paru</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2">Bunyi Nafas Dasar</label>
                                <select wire:model.defer="dataFisik.paru.bunyi_nafas" class="block w-full rounded-lg border-slate-200 bg-white text-sm font-medium focus:border-blue-500 focus:ring-blue-500 py-2 cursor-pointer">
                                    <option value="Vesikular">Vesikular</option><option value="Bronkial">Bronkial</option><option value="Bronkovesikular">Bronkovesikular</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2">Bunyi Nafas Tambahan</label>
                                <select wire:model.defer="dataFisik.paru.bunyi_nafas_tambahan" class="block w-full rounded-lg border-slate-200 bg-white text-sm font-medium focus:border-blue-500 focus:ring-blue-500 py-2 cursor-pointer">
                                    <option value="Tidak ada">Tidak ada</option><option value="Ronkhi">Ronkhi</option>
                                    <option value="Wheezing">Wheezing</option><option value="Stridor">Stridor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Abdomen & Ekstremitas --}}
                <div class="space-y-6">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Abdomen</h4>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach (['peristaltik' => 'Peristaltik', 'nyeri_tekan' => 'Nyeri Tekan', 'massa' => 'Massa', 'hati' => 'Hati', 'limpa' => 'Limpa'] as $key => $label)
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-2">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.abdomen.{{ $key }}" class="block w-full rounded-lg border-slate-200 bg-white text-xs font-medium focus:border-blue-500 focus:ring-blue-500 py-1.5 cursor-pointer">
                                        <option value="Dalam batas normal">Dalam batas normal</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Ada/Pembesaran">Ada/Pembesaran</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h4 class="font-bold text-sm text-slate-700 mb-4 border-b border-slate-200 pb-2">Ekstremitas & Refleks</h4>
                        <div class="space-y-4 text-xs font-medium">
                            <select wire:model.defer="dataFisik.ekstremitas.ekstremitas" class="block w-full rounded-lg border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 py-2 cursor-pointer">
                                <option value="Dalam Batas Normal">Ekstremitas: Dalam Batas Normal</option><option value="Edema Kanan">Edema Kanan</option><option value="Edema Kiri">Edema Kiri</option><option value="Kelainan Lain">Kelainan Lain</option>
                            </select>
                            <div class="grid grid-cols-2 gap-3">
                                <select wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kanan" class="block w-full rounded-lg border-slate-200 bg-white focus:border-blue-500 py-1.5"><option value="+2">Fisiologis Kanan (+2)</option><option value="+1">Menurun (+1)</option><option value="+3">Meningkat (+3)</option></select>
                                <select wire:model.defer="dataFisik.ekstremitas.refleks_fisiologis_kiri" class="block w-full rounded-lg border-slate-200 bg-white focus:border-blue-500 py-1.5"><option value="+2">Fisiologis Kiri (+2)</option><option value="+1">Menurun (+1)</option><option value="+3">Meningkat (+3)</option></select>
                                <select wire:model.defer="dataFisik.ekstremitas.refleks_patologis_kanan" class="block w-full rounded-lg border-slate-200 bg-white focus:border-blue-500 py-1.5"><option value="Tidak Ada">Patologis Kanan (Tidak Ada)</option><option value="Ada">Ada</option></select>
                                <select wire:model.defer="dataFisik.ekstremitas.refleks_patologis_kiri" class="block w-full rounded-lg border-slate-200 bg-white focus:border-blue-500 py-1.5"><option value="Tidak Ada">Patologis Kiri (Tidak Ada)</option><option value="Ada">Ada</option></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. RIWAYAT PAJANAN PEKERJAAN --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">5. Riwayat Pajanan Pekerjaan</h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Kiri: Fisik & Kimia --}}
                <div class="space-y-6">
                    <div class="border border-indigo-100 rounded-xl overflow-hidden">
                        <div class="bg-indigo-50 px-4 py-2 border-b border-indigo-100"><h4 class="font-bold text-sm text-indigo-800">A. Bahaya Fisik</h4></div>
                        <div class="p-4 space-y-3">
                            @foreach (['kebisingan' => 'Kebisingan', 'suhu_panas' => 'Suhu panas', 'suhu_dingin' => 'Suhu dingin', 'radiasi_non_pengion' => 'Radiasi bukan pengion', 'radiasi_pengion' => 'Radiasi pengion', 'getaran_lokal' => 'Getaran lokal', 'getaran_seluruh_tubuh' => 'Getaran seluruh tubuh', 'ketinggian' => 'Ketinggian', 'lain_fisik' => 'Lain - lain'] as $key => $label)
                                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-slate-100 hover:border-indigo-200 transition-colors">
                                    <label class="text-xs font-semibold text-slate-600 w-3/4">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.pajanan.fisik.{{ $key }}" class="block w-24 rounded-lg border-slate-200 bg-slate-50 text-xs font-bold focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer py-1 px-2">
                                        <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border border-emerald-100 rounded-xl overflow-hidden">
                        <div class="bg-emerald-50 px-4 py-2 border-b border-emerald-100"><h4 class="font-bold text-sm text-emerald-800">B. Bahaya Kimia</h4></div>
                        <div class="p-4 space-y-3">
                            @foreach (['debu_anorganik' => 'Debu anorganik (Silika, semen)', 'debu_organic' => 'Debu organic (Kapas, gandum)', 'asap' => 'Asap', 'logam_berat' => 'Logam berat', 'iritan_asam' => 'Iritan asam', 'iritan_basa' => 'Iritan basa', 'cairan_pembersih' => 'Cairan pembersih', 'pestisida' => 'Pestisida', 'uap_logam' => 'Uap logam', 'lain_kimia' => 'Lain - lain'] as $key => $label)
                                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-slate-100 hover:border-emerald-200 transition-colors">
                                    <label class="text-xs font-semibold text-slate-600 w-3/4 truncate pr-2" title="{{ $label }}">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.pajanan.kimia.{{ $key }}" class="block w-24 rounded-lg border-slate-200 bg-slate-50 text-xs font-bold focus:border-emerald-500 focus:ring-emerald-500 cursor-pointer py-1 px-2">
                                        <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kanan: Biologi, Psikologi, Ergonomis --}}
                <div class="space-y-6">
                    <div class="border border-rose-100 rounded-xl overflow-hidden">
                        <div class="bg-rose-50 px-4 py-2 border-b border-rose-100"><h4 class="font-bold text-sm text-rose-800">C. Bahaya Biologi</h4></div>
                        <div class="p-4 space-y-3">
                            @foreach (['bakteri' => 'Bakteri / Virus / Jamur', 'darah' => 'Darah / Cairan tubuh', 'nyamuk' => 'Nyamuk / Serangga', 'limbah' => 'Limbah', 'lain_biologi' => 'Lain - lain'] as $key => $label)
                                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-slate-100 hover:border-rose-200 transition-colors">
                                    <label class="text-xs font-semibold text-slate-600 w-3/4">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.pajanan.biologi.{{ $key }}" class="block w-24 rounded-lg border-slate-200 bg-slate-50 text-xs font-bold focus:border-rose-500 focus:ring-rose-500 cursor-pointer py-1 px-2">
                                        <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border border-purple-100 rounded-xl overflow-hidden">
                        <div class="bg-purple-50 px-4 py-2 border-b border-purple-100"><h4 class="font-bold text-sm text-purple-800">D. Psikologi & Ergonomis</h4></div>
                        <div class="p-4 space-y-3">
                            @foreach (['beban_kerja' => 'Beban kerja tidak sesuai', 'pekerjaan_tidak_sesuai' => 'Pekerjaan tidak sesuai skill', 'bekerja_giliran' => 'Bekerja shift', 'konflik_teman_sekerja' => 'Konflik kerja', 'gerakan_berulang' => 'Gerakan tangan berulang', 'angkat_berat' => 'Angkat/angkut berat', 'duduk_lama' => 'Duduk lama > 4 jam', 'berdiri_lama' => 'Berdiri lama > 4 jam', 'posisi_tidak_ergonomis' => 'Posisi tubuh tdk ergonomis', 'bekerja_layar_monitor' => 'Menatap monitor > 4 jam'] as $key => $label)
                                <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-slate-100 hover:border-purple-200 transition-colors">
                                    <label class="text-xs font-semibold text-slate-600 w-3/4 truncate pr-2" title="{{ $label }}">{{ $label }}</label>
                                    <select wire:model.defer="dataFisik.pajanan.{{ in_array($key, ['gerakan_berulang','angkat_berat','duduk_lama','berdiri_lama','posisi_tidak_ergonomis','bekerja_layar_monitor']) ? 'ergonomis' : 'psikologi' }}.{{ $key }}" class="block w-24 rounded-lg border-slate-200 bg-slate-50 text-xs font-bold focus:border-purple-500 focus:ring-purple-500 cursor-pointer py-1 px-2">
                                        <option value="Tidak">Tidak</option><option value="Ya">Ya</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. DIAGNOSA & KESIMPULAN --}}
        <div class="bg-amber-50 rounded-2xl border border-amber-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-amber-200/50 bg-amber-100/50">
                <h3 class="text-base font-bold text-amber-900">6. Kesimpulan & Diagnosa Fisik</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-amber-800 mb-2">Ringkasan Hasil Klinis</label>
                    <textarea wire:model.defer="kesimpulan" rows="3" class="block w-full rounded-xl border-amber-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-500 placeholder-amber-200 transition-colors resize-none" placeholder="Tuliskan ringkasan..."></textarea>
                    @error('kesimpulan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-amber-800 mb-2">Saran & Tindak Lanjut</label>
                    <textarea wire:model.defer="keterangan" rows="3" class="block w-full rounded-xl border-amber-300 bg-white text-sm focus:border-amber-500 focus:ring-amber-500 placeholder-amber-200 transition-colors resize-none" placeholder="Saran perbaikan untuk pasien..."></textarea>
                    @error('keterangan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-slate-200">
            
            {{-- Tombol Lihat PDF (Menggunakan helper asset agar URL menyertakan folder /storage/) --}}
            @if($fisikResult && $fisikResult->file_path)
                <a href="{{ asset('storage/' . $fisikResult->file_path) }}" target="_blank"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-emerald-500 text-emerald-600 font-bold rounded-xl shadow-sm hover:bg-emerald-50 transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i> Lihat Laporan PDF
                </a>
            @endif
            
            <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                <span wire:loading.remove wire:target="simpanHasil"><i class="fas fa-save mr-2"></i> Simpan Pemeriksaan Fisik</span>
                <span wire:loading wire:target="simpanHasil"><i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan Data...</span>
            </button>
        </div>
    </form>
</div>