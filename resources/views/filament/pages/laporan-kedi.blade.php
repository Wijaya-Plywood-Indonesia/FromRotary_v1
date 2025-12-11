<x-filament-panels::page>
    <div>

        {{-- FILTER --}}
<<<<<<< HEAD
        <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow mb-4 text-gray-800 dark:text-white">
            {{ $this->form }}
        </div>

        {{-- LOADING --}}
        <div wire:loading wire:target="loadAllData" class="text-center text-gray-500 dark:text-gray-400 py-4">
=======
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow mb-4">
            {{ $this->form }}
        </div>

        <div wire:loading wire:target="loadAllData" class="text-center text-gray-500 py-4">
>>>>>>> jh
            Memuat data...
        </div>

        <div wire:loading.remove>

            @forelse($dataKedi as $data)

<<<<<<< HEAD
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
=======
                <div class="mb-8 border border-gray-700 rounded-lg overflow-hidden bg-gray-900 text-white">

                    {{-- HEADER --}}
                    <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between">
                        <h3 class="font-bold text-lg">
                            PRODUKSI KEDI 
                            – {{ $data['tanggal_produksi'] }}
                            (Kode: {{ $data['kode_kedi'] ?? '-' }})
                        </h3>

                        @php
                            $statusColor = $data['status'] === 'masuk'
                                ? 'bg-blue-600'
                                : 'bg-green-600';
                        @endphp

                        <span class="px-3 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                            STATUS : {{ strtoupper($data['status']) }}
>>>>>>> jh
                        </span>
                    </div>

                    {{-- DETAIL MASUK --}}
                    @if($data['status'] === 'masuk')
<<<<<<< HEAD
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
=======
                        <div class="p-4 bg-gray-800/50 border-b border-gray-700">
                            <h4 class="font-semibold mb-2">Detail Kayu Masuk</h4>

                            <table class="w-full text-sm">
                                <thead class="bg-gray-700 text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-6 py-3">No Palet</th>
                                        <th class="px-6 py-3">Kode Kedi</th>
                                        <th class="px-6 py-3">Ukuran</th>
                                        <th class="px-6 py-3">Jenis Kayu</th>
                                        <th class="px-6 py-3">KW</th>
                                        <th class="px-6 py-3">Jumlah</th>
                                        <th class="px-6 py-3">Rencana Bongkar</th>
>>>>>>> jh
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_masuk'] as $item)
<<<<<<< HEAD
                                        <tr class="border-b border-gray-300 dark:border-gray-700 text-center
                                                   hover:bg-gray-200 dark:hover:bg-gray-700 transition">

                                            <td class="px-3 py-2">{{ $item['no_palet'] }}</td>
                                            <td class="px-3 py-2">{{ $item['mesin'] }}</td>
                                            <td class="px-3 py-2">{{ $item['ukuran'] }}</td>
                                            <td class="px-3 py-2">{{ $item['jenis_kayu'] }}</td>
                                            <td class="px-3 py-2">{{ $item['kw'] }}</td>
                                            <td class="px-3 py-2">{{ number_format($item['jumlah'],0,',','.') }}</td>
                                            <td class="px-3 py-2 text-orange-600 dark:text-orange-400">
=======
                                        <tr class="border-b border-gray-800 text-center">
                                            <td class="px-6 py-3">{{ $item['no_palet'] }}</td>
                                            <td class="px-6 py-3">{{ $item['kode_kedi'] }}</td>
                                            <td class="px-6 py-3">{{ $item['ukuran'] }}</td>
                                            <td class="px-6 py-3">{{ $item['jenis_kayu'] }}</td>
                                            <td class="px-6 py-3">{{ $item['kw'] }}</td>
                                            <td class="px-6 py-3">{{ number_format($item['jumlah'],0,',','.') }}</td>
                                            <td class="px-6 py-3 text-yellow-400">
>>>>>>> jh
                                                {{ $item['rencana_bongkar'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
<<<<<<< HEAD
                                            <td colspan="7" class="py-4 text-center text-gray-500 dark:text-gray-400">
=======
                                            <td colspan="7" class="text-center py-4 text-gray-400">
>>>>>>> jh
                                                Tidak ada data masuk.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
<<<<<<< HEAD

=======
>>>>>>> jh
                            </table>
                        </div>
                    @endif

<<<<<<< HEAD

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
=======
                    {{-- DETAIL BONGKAR --}}
                    @if($data['status'] === 'bongkar')
                        <div class="p-4 bg-gray-800/50">
                            <h4 class="font-semibold mb-2">Detail Bongkar</h4>

                            <table class="w-full text-sm">
                                <thead class="bg-gray-700 text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-6 py-3">No Palet</th>
                                        <th class="px-6 py-3">Kode Kedi</th>
                                        <th class="px-6 py-3">Ukuran</th>
                                        <th class="px-6 py-3">Jenis Kayu</th>
                                        <th class="px-6 py-3">KW</th>
                                        <th class="px-6 py-3">Jumlah</th>
>>>>>>> jh
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_bongkar'] as $item)
<<<<<<< HEAD
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
=======
                                        <tr class="border-b border-gray-800 text-center">
                                            <td class="px-6 py-3">{{ $item['no_palet'] }}</td>
                                            <td class="px-6 py-3">{{ $item['kode_kedi'] }}</td>
                                            <td class="px-6 py-3">{{ $item['ukuran'] }}</td>
                                            <td class="px-6 py-3">{{ $item['jenis_kayu'] }}</td>
                                            <td class="px-6 py-3">{{ $item['kw'] }}</td>
                                            <td class="px-6 py-3">{{ number_format($item['jumlah'],0,',','.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-gray-400">
>>>>>>> jh
                                                Tidak ada data bongkar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
<<<<<<< HEAD

=======
>>>>>>> jh
                            </table>
                        </div>
                    @endif

                </div>

            @empty
<<<<<<< HEAD
                <div class="p-6 text-center bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg">
=======
                <div class="p-6 text-center text-gray-400 bg-gray-800 rounded-lg">
>>>>>>> jh
                    Tidak ada data produksi Kedi untuk tanggal ini.
                </div>
            @endforelse

        </div>

    </div>
</x-filament-panels::page>
