{{-- Peta Gigi menggunakan penomoran FDI (11-18, 21-28, 31-38, 41-48) --}}
<div class="w-full max-w-xl mx-auto">
    <table class="w-full border-collapse">
        <thead class="text-center font-bold text-gray-700">
            <tr>
                @for ($i = 8; $i >= 1; $i--) <td class="py-1 px-1 text-sm">{{ $i }}</td> @endfor
                @for ($i = 1; $i <= 8; $i++) <td class="py-1 px-1 text-sm">{{ $i }}</td> @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                {{-- Kuadran 1 (Atas Kanan) --}}
                @for ($i = 8; $i >= 1; $i--)
                    @php $gigiId = '1' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                    <td class="p-1">
                        <div id="gigi-{{ $gigiId }}" 
                            wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                            class="tooth-box w-full h-8 flex items-center justify-center border-2 rounded-md transition duration-150 cursor-pointer 
                                @if($status === 'Caries') bg-red-500 border-red-700 text-white font-bold 
                                @elseif($status === 'Missing') bg-gray-400 border-gray-600 text-black font-bold 
                                @elseif($status === 'Tambal') bg-green-500 border-green-700 text-white font-bold
                                @else bg-gray-200 border-gray-400 hover:bg-yellow-200 @endif">
                            {{ $gigiId }}
                            @if($status === 'Caries')
                                <span class="absolute text-xl">O</span> {{-- Lingkaran hitam untuk karies --}}
                            @elseif($status === 'Missing')
                                <span class="absolute text-2xl font-black">X</span> {{-- X besar untuk hilang --}}
                            @elseif($status === 'Tambal')
                                <span class="absolute text-lg font-bold">T</span> {{-- T untuk tambal (Restorasi) --}}
                            @endif
                        </div>
                    </td>
                @endfor
                {{-- Kuadran 2 (Atas Kiri) --}}
                @for ($i = 1; $i <= 8; $i++)
                    @php $gigiId = '2' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                    <td class="p-1">
                        <div id="gigi-{{ $gigiId }}" 
                            wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                            class="tooth-box w-full h-8 flex items-center justify-center border-2 rounded-md transition duration-150 cursor-pointer 
                                @if($status === 'Caries') bg-red-500 border-red-700 text-white font-bold 
                                @elseif($status === 'Missing') bg-gray-400 border-gray-600 text-black font-bold 
                                @elseif($status === 'Tambal') bg-green-500 border-green-700 text-white font-bold
                                @else bg-gray-200 border-gray-400 hover:bg-yellow-200 @endif">
                            {{ $gigiId }}
                            @if($status === 'Caries')
                                <span class="absolute text-xl">O</span>
                            @elseif($status === 'Missing')
                                <span class="absolute text-2xl font-black">X</span>
                            @elseif($status === 'Tambal')
                                <span class="absolute text-lg font-bold">T</span>
                            @endif
                        </div>
                    </td>
                @endfor
            </tr>
            <tr><td colspan="16" class="border-t border-b border-gray-400 h-1"></td></tr> {{-- Garis Tengah --}}
            <tr>
                {{-- Kuadran 4 (Bawah Kanan) --}}
                @for ($i = 8; $i >= 1; $i--)
                    @php $gigiId = '4' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                    <td class="p-1">
                        <div id="gigi-{{ $gigiId }}" 
                            wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                            class="tooth-box w-full h-8 flex items-center justify-center border-2 rounded-md transition duration-150 cursor-pointer 
                                @if($status === 'Caries') bg-red-500 border-red-700 text-white font-bold 
                                @elseif($status === 'Missing') bg-gray-400 border-gray-600 text-black font-bold 
                                @elseif($status === 'Tambal') bg-green-500 border-green-700 text-white font-bold
                                @else bg-gray-200 border-gray-400 hover:bg-yellow-200 @endif">
                            {{ $gigiId }}
                            @if($status === 'Caries')
                                <span class="absolute text-xl">O</span>
                            @elseif($status === 'Missing')
                                <span class="absolute text-2xl font-black">X</span>
                            @elseif($status === 'Tambal')
                                <span class="absolute text-lg font-bold">T</span>
                            @endif
                        </div>
                    </td>
                @endfor
                {{-- Kuadran 3 (Bawah Kiri) --}}
                @for ($i = 1; $i <= 8; $i++)
                    @php $gigiId = '3' . $i; $status = $gigiKlinis[$gigiId] ?? 'Normal'; @endphp
                    <td class="p-1">
                        <div id="gigi-{{ $gigiId }}" 
                            wire:click.prevent="toggleGigiKlinis('{{ $gigiId }}')" 
                            class="tooth-box w-full h-8 flex items-center justify-center border-2 rounded-md transition duration-150 cursor-pointer 
                                @if($status === 'Caries') bg-red-500 border-red-700 text-white font-bold 
                                @elseif($status === 'Missing') bg-gray-400 border-gray-600 text-black font-bold 
                                @elseif($status === 'Tambal') bg-green-500 border-green-700 text-white font-bold
                                @else bg-gray-200 border-gray-400 hover:bg-yellow-200 @endif">
                            {{ $gigiId }}
                            @if($status === 'Caries')
                                <span class="absolute text-xl">O</span>
                            @elseif($status === 'Missing')
                                <span class="absolute text-2xl font-black">X</span>
                            @elseif($status === 'Tambal')
                                <span class="absolute text-lg font-bold">T</span>
                            @endif
                        </div>
                    </td>
                @endfor
            </tr>
        </tbody>
    </table>
    
</div>
