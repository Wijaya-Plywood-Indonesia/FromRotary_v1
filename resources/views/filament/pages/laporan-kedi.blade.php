<x-filament-panels::page>
    <div>

        {{-- FILTER --}}
        <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow mb-4 text-gray-800 dark:text-white">
            {{ $this->form }}
        </div>

        {{-- LOADING --}}
        <div wire:loading wire:target="loadAllData" class="text-center text-gray-500 dark:text-gray-400 py-4">
            Memuat data...
        </div>

        <div wire:loading.remove>

            @forelse($dataKedi as $data)

                <div class="mb-8 border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden 
                            bg-gray-100 dark:bg-gray-800 
                            text-gray-800 dark:text-white shadow">

                    {{-- HEADER --}}
                    <div class="p-4 bg-gray-200 dark:bg-gray-700 
                                border-b border-gray-300 dark:border-gray-600
                                flex justify-between items-center">

                        <h3 class="font-bold text-lg">
                            PRODUKSI KEDI – {{ $data['tanggal_produksi'] }}
                        </h3>

                        @php
                            $isMasuk = $data['status'] === 'masuk';

                            $statusClass = $isMasuk
                                ? 'bg-green-200 text-green-900 dark:bg-green-700 dark:text-white'
                                : 'bg-red-200 text-red-900 dark:bg-red-700 dark:text-white';

                            $statusLabel = $isMasuk ? 'MASUK' : 'BONGKAR';
                        @endphp

                        <span class="px-4 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                            STATUS : {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- DETAIL MASUK --}}
                    @if($data['status'] === 'masuk')
                        <div class="p-4 bg-gray-100 dark:bg-gray-800">

                            <h4 class="font-semibold mb-3">
                                Detail Masuk Kedi
                            </h4>

                            <table class="w-full text-sm">

                                <thead class="bg-gray-300 dark:bg-gray-700 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 border dark:border-gray-600">No Palet</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Mesin</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Ukuran</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Jenis Kayu</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">KW</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Jumlah</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Rencana Bongkar</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_masuk'] as $item)
                                        <tr class="border-b border-gray-300 dark:border-gray-700 text-center
                                                   hover:bg-gray-200 dark:hover:bg-gray-700 transition">

                                            <td class="px-3 py-2">{{ $item['no_palet'] }}</td>
                                            <td class="px-3 py-2">{{ $item['mesin'] }}</td>
                                            <td class="px-3 py-2">{{ $item['ukuran'] }}</td>
                                            <td class="px-3 py-2">{{ $item['jenis_kayu'] }}</td>
                                            <td class="px-3 py-2">{{ $item['kw'] }}</td>
                                            <td class="px-3 py-2">{{ number_format($item['jumlah'],0,',','.') }}</td>
                                            <td class="px-3 py-2 text-orange-600 dark:text-orange-400">
                                                {{ $item['rencana_bongkar'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                                Tidak ada data masuk.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    @endif


                    {{-- DETAIL BONGKAR --}}
                    @if($data['status'] === 'bongkar')
                        <div class="p-4 bg-gray-100 dark:bg-gray-800">

                            <h4 class="font-semibold mb-3">
                                Detail Bongkar Kedi
                            </h4>

                            <table class="w-full text-sm">

                                <thead class="bg-gray-300 dark:bg-gray-700 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 border dark:border-gray-600">No Palet</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Mesin</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Ukuran</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Jenis Kayu</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">KW</th>
                                        <th class="px-3 py-2 border dark:border-gray-600">Jumlah</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_bongkar'] as $item)
                                        <tr class="border-b border-gray-300 dark:border-gray-700 text-center
                                                   hover:bg-gray-200 dark:hover:bg-gray-700 transition">

                                            <td class="px-3 py-2">{{ $item['no_palet'] }}</td>
                                            <td class="px-3 py-2">{{ $item['mesin'] }}</td>
                                            <td class="px-3 py-2">{{ $item['ukuran'] }}</td>
                                            <td class="px-3 py-2">{{ $item['jenis_kayu'] }}</td>
                                            <td class="px-3 py-2">{{ $item['kw'] }}</td>
                                            <td class="px-3 py-2">{{ number_format($item['jumlah'],0,',','.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                                Tidak ada data bongkar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    @endif

                </div>

            @empty
                <div class="p-6 text-center bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg">
                    Tidak ada data produksi Kedi untuk tanggal ini.
                </div>
            @endforelse

        </div>

    </div>
</x-filament-panels::page>
