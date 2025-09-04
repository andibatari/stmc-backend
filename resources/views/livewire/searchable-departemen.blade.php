<div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div x-data="{ open: @entangle('isDepartemenDropdownOpen') }" @click.away="open = false" class="relative">
            <label class="block text-sm font-medium text-gray-700">Departemen</label>
            <button type="button" @click="$wire.toggleDepartemenDropdown()" class="mt-1 flex justify-between items-center w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-left bg-white border">
                <span class="text-gray-900">{{ $selectedDepartemenName ?: 'Pilih Departemen' }}</span>
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg" x-cloak>
                <div class="p-2 border-b flex items-center">
                    <input type="text" wire:model.live="searchDepartemen" placeholder="Cari Departemen..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <button wire:click.prevent="toggleAddDepartemen" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-md ml-2 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </button>
                </div>

                @if ($isAddingNewDepartemen)
                    <div class="p-2 flex items-center border-b">
                        <input type="text" wire:model.live="newDepartemenName" placeholder="Nama Departemen Baru" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <button wire:click.prevent="addNewDepartemen" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 ml-2 rounded-md text-sm">Simpan</button>
                    </div>
                @endif

                <ul class="py-1 max-h-60 overflow-y-auto">
                    @foreach($departemens as $departemen)
                        <li wire:click="selectDepartemen({{ $departemen->id }}, '{{ $departemen->nama_departemen }}')" class="cursor-pointer hover:bg-gray-100 p-2">
                            {{ $departemen->nama_departemen }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div x-data="{ open: @entangle('isUnitKerjaDropdownOpen') }" @click.away="open = false" class="relative">
            <label class="block text-sm font-medium text-gray-700">Unit Kerja</label>
            <button type="button" @click="$wire.toggleUnitKerjaDropdown()" class="mt-1 flex justify-between items-center w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-left bg-white border" @if(!$selectedDepartemenId) disabled @endif>
                <span class="text-gray-900">{{ $selectedUnitKerjaName ?: 'Pilih Unit Kerja' }}</span>
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg" x-cloak>
                <div class="p-2 border-b flex items-center">
                    <input type="text" wire:model.live="searchUnitKerja" placeholder="Cari Unit Kerja..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 mr-2">
                    <button wire:click.prevent="$set('isAddingNewUnitKerja', true)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-md transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </button>
                </div>
                @if ($isAddingNewUnitKerja)
                <div class="p-2 flex items-center border-b">
                    <input type="text" wire:model.live="newUnitKerjaName" placeholder="Nama Unit Kerja Baru" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <button wire:click.prevent="addNewUnitKerja" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 ml-2 rounded-md text-sm">Simpan</button>
                </div>
                @endif
                <ul class="py-1 max-h-60 overflow-y-auto">
                    @foreach($unitKerjas as $unitKerja)
                        <li wire:click="selectUnitKerja({{ $unitKerja->id }}, '{{ $unitKerja->nama_unit_kerja }}')" class="cursor-pointer hover:bg-gray-100 p-2">
                            {{ $unitKerja->nama_unit_kerja }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    {{-- <input type="hidden" name="departemens_id" value="{{ $selectedDepartemenId }}">
    <input type="hidden" name="unit_kerjas_id" value="{{ $selectedUnitKerjaId }}"> --}}
</div>
