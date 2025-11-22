<x-filament-panels::page>
    <div>

        {{-- Form Filter Tanggal --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow mb-4">
            {{ $this->form }}
        </div>

        <div wire:loading wire:target="loadAllData" class="w-full text-center py-4">
            <span class="text-gray-500">Memuat data...</span>
        </div>

        <div wire:loading.remove>

            @forelse($dataKedi as $data)

                <div class="mb-10 border border-gray-700 rounded-lg overflow-hidden bg-gray-900 text-white shadow-lg">

                    {{-- HEADER PRODUKSI KEDI --}}
                    <div class="p-4 bg-gray-800 border-b border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">
                            PRODUKSI KEDI #{{ $data['id_produksi'] ?? '-' }} -
                            {{ $data['tanggal_produksi'] ?? '-' }}
                        </h3>

                        @php
                            $statusColor = match($data['status']) {
                                'Selesai' => 'bg-green-600',
                                'Dalam Proses' => 'bg-yellow-600',
                                default => 'bg-red-600',
                            };
                        @endphp

                        <span class="px-3 py-1 text-xs font-semibold rounded {{ $statusColor }} text-white">
                            Status: {{ $data['status'] ?? 'N/A' }}
                        </span>
                    </div>

                    {{-- DETAIL MASUK --}}
                    <div class="p-4 bg-gray-800/50 border-b border-gray-700">
                        <h4 class="text-md font-semibold text-gray-300 mb-2 border-b border-gray-700 pb-1">
                            Detail Kayu Masuk (Kedi)
                        </h4>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-300">
                                <thead class="text-xs text-gray-400 uppercase bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">No. Palet</th>
                                        <th class="px-6 py-3">Ukuran</th>
                                        <th class="px-6 py-3">Jenis Kayu</th>
                                        <th class="px-6 py-3">KW</th>
                                        <th class="px-6 py-3">Jumlah</th>
                                        <th class="px-6 py-3">Rencana Bongkar</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_masuk'] as $masuk)
                                        <tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800">
                                            <td class="px-6 py-4 text-white">{{ $masuk['no_palet'] }}</td>
                                            <td class="px-6 py-4">{{ $masuk['ukuran'] }}</td>
                                            <td class="px-6 py-4">{{ $masuk['jenis_kayu'] }}</td>
                                            <td class="px-6 py-4">{{ $masuk['kw'] }}</td>
                                            <td class="px-6 py-4">
                                                {{ number_format($masuk['jumlah'] ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-yellow-400">
                                                {{ $masuk['rencana_bongkar'] ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                Tidak ada detail kayu masuk.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>

                    {{-- DETAIL BONGKAR --}}
                    <div class="p-4 bg-gray-900/50">
                        <h4 class="text-md font-semibold text-gray-300 mb-2 border-b border-gray-700 pb-1">
                            Detail Bongkar (Hasil)
                        </h4>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-300">
                                <thead class="text-xs text-gray-400 uppercase bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">No. Palet</th>
                                        <th class="px-6 py-3">Ukuran</th>
                                        <th class="px-6 py-3">Jenis Kayu</th>
                                        <th class="px-6 py-3">KW</th>
                                        <th class="px-6 py-3">Hasil Bongkar</th>
                                        <th class="px-6 py-3">Tanggal Bongkar</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data['detail_bongkar'] as $bongkar)
                                        <tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800">
                                            <td class="px-6 py-4">{{ $bongkar['no_palet'] }}</td>
                                            <td class="px-6 py-4">{{ $bongkar['ukuran'] }}</td>
                                            <td class="px-6 py-4">{{ $bongkar['jenis_kayu'] }}</td>
                                            <td class="px-6 py-4">{{ $bongkar['kw'] }}</td>
                                            <td class="px-6 py-4 text-green-400">
                                                {{ number_format($bongkar['jumlah'] ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4">{{ $bongkar['tanggal_bongkar'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                Belum ada detail bongkar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>

            @empty
                <div class="p-6 text-center bg-gray-800 rounded-lg">
                    <p class="text-gray-400">Belum ada data produksi Kedi pada tanggal ini.</p>
                </div>
            @endforelse

        </div>

    </div>
</x-filament-panels::page>
