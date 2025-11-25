<x-filament-panels::page>
    <div>

        {{-- FILTER --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow mb-4">
            {{ $this->form }}
        </div>

        <div wire:loading wire:target="loadAllData" class="text-center text-gray-500 py-4">
            Memuat data...
        </div>

        <div wire:loading.remove>

            @forelse($dataKedi as $data)

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
                        </span>
                    </div>

                    {{-- DETAIL MASUK --}}
                    @if($data['status'] === 'masuk')
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
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_masuk'] as $item)
                                        <tr class="border-b border-gray-800 text-center">
                                            <td class="px-6 py-3">{{ $item['no_palet'] }}</td>
                                            <td class="px-6 py-3">{{ $item['kode_kedi'] }}</td>
                                            <td class="px-6 py-3">{{ $item['ukuran'] }}</td>
                                            <td class="px-6 py-3">{{ $item['jenis_kayu'] }}</td>
                                            <td class="px-6 py-3">{{ $item['kw'] }}</td>
                                            <td class="px-6 py-3">{{ number_format($item['jumlah'],0,',','.') }}</td>
                                            <td class="px-6 py-3 text-yellow-400">
                                                {{ $item['rencana_bongkar'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-gray-400">
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
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_bongkar'] as $item)
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
                <div class="p-6 text-center text-gray-400 bg-gray-800 rounded-lg">
                    Tidak ada data produksi Kedi untuk tanggal ini.
                </div>
            @endforelse

        </div>

    </div>
</x-filament-panels::page>
