<x-filament-panels::page>
    {{-- FORM INPUT TANGGAL --}}
    <div class="p-4 bg-white dark:bg-zinc-900 rounded-lg shadow">
        {{ $this->form }}
    </div>

    {{-- LOADING OVERLAY --}}
    @if ($isLoading)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-75 dark:bg-zinc-900 dark:bg-opacity-75"
    >
        <div class="flex items-center space-x-3">
            <x-filament::loading-indicator class="w-8 h-8 text-primary-600" />
            <span class="text-lg font-medium text-zinc-700 dark:text-zinc-300">
                Memuat data...
            </span>
        </div>
    </div>
    @endif @php $dataProduksi = $dataProduksi ?? []; $groupedByMesin =
    collect($dataProduksi)->groupBy('mesin'); @endphp

    {{-- LISTING DATA BERDASARKAN MESIN --}}
    <div class="space-y-12 mt-6">
        @forelse ($groupedByMesin as $mesinNama => $produksiList) @php $first =
        $produksiList->first() ?? []; $pekerja = $first['pekerja'] ?? [];
        $kodeUkuran = $first['kode_ukuran'] ?? 'TIDAK ADA UKURAN'; $totalPekerja
        = count($pekerja); $hasil = $first['total_target_harian'] ?? 0; $target
        = $first['target'] ?? 0; $selisih = $first['selisih'] ?? 0; $jamKerja =
        $first['jam_kerja'] ?? 0; $warna = $selisih >= 0 ? 'text-green-400' :
        'text-red-400'; $tanda = $selisih >= 0 ? '+' : ''; @endphp

        {{-- CARD PER MESIN --}}
        <div
            class="bg-white dark:bg-zinc-900 rounded-sm shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
        >
            <div class="bg-zinc-800 p-4 text-white">
                <h2 class="text-lg font-bold text-center">
                    PEKERJA MESIN: {{ strtoupper($mesinNama) }} -
                    {{ strtoupper($kodeUkuran) }}
                </h2>
            </div>

            <div class="p-4">
                <div class="w-full overflow-x-auto">
                    <div class="min-w-[800px]">
                        {{-- TABLE --}}
                        <table
                            class="w-full text-sm border-collapse border border-zinc-300 dark:border-zinc-600"
                        >
                            <thead>
                                <tr>
                                    <th
                                        colspan="7"
                                        class="p-4 text-xl font-bold text-center bg-zinc-700 text-white"
                                    >
                                        DATA PEKERJA
                                    </th>
                                </tr>

                                <tr
                                    class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300 border-t"
                                >
                                    <th class="p-2 text-center text-xs w-16">
                                        ID
                                    </th>
                                    <th class="p-2 text-left text-xs w-40">
                                        Nama
                                    </th>
                                    <th class="p-2 text-center text-xs w-20">
                                        Masuk
                                    </th>
                                    <th class="p-2 text-center text-xs w-20">
                                        Pulang
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Ijin
                                    </th>
                                    <th class="p-2 text-right text-xs w-36">
                                        Potongan Target
                                    </th>
                                    <th class="p-2 text-left text-xs">
                                        Keterangan
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pekerja as $i => $p)
                                <tr
                                    class="{{
                                        $i % 2
                                            ? 'bg-zinc-50 dark:bg-zinc-800/50'
                                            : 'bg-white dark:bg-zinc-900'
                                    }}"
                                >
                                    <td
                                        class="p-2 text-center text-xs border-r"
                                    >
                                        {{ $p["id"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-left text-xs border-r font-medium"
                                    >
                                        {{ $p["nama"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r"
                                    >
                                        {{ $p["jam_masuk"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r"
                                    >
                                        {{ $p["jam_pulang"] ?? "-" }}
                                    </td>

                                    <td
                                        class="p-2 text-center text-xs border-r text-yellow-600 dark:text-yellow-400"
                                    >
                                        {{ $p["ijin"] ?? "-" }}
                                    </td>

                                    <td
                                        class="p-2 text-right text-xs border-r font-bold {{
                                            ($p['selisih'] ?? 0) < 0
                                                ? 'text-red-600 dark:text-red-400'
                                                : ''
                                        }}"
                                    >
                                        Rp
                                        {{
                                            number_format($p["pot_target"] ?? 0)
                                        }}
                                    </td>

                                    <td class="p-2 text-left text-xs">
                                        {{ $p["keterangan"] ?? "-" }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td
                                        colspan="7"
                                        class="p-4 text-center text-zinc-500"
                                    >
                                        Tidak ada data pekerja untuk mesin ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                            <tfoot
                                class="bg-zinc-100 dark:bg-zinc-800 border-t-2"
                            >
                                <tr>
                                    <td
                                        colspan="7"
                                        class="p-3 text-center text-xs text-zinc-600"
                                    >
                                        <span class="font-medium"
                                            >Pekerja:</span
                                        >
                                        <strong>{{ $totalPekerja }}</strong>

                                        <span class="mx-2 text-zinc-400"
                                            >|</span
                                        >

                                        <span class="font-medium">Target:</span>
                                        <strong>{{
                                            number_format($target)
                                        }}</strong>

                                        <span class="mx-2 text-zinc-400"
                                            >|</span
                                        >

                                        <span class="font-medium"
                                            >Jam Produksi:</span
                                        >
                                        <strong>{{
                                            number_format($jamKerja)
                                        }}</strong>

                                        <span class="mx-2 text-zinc-400"
                                            >|</span
                                        >

                                        <span class="font-medium">Hasil:</span>
                                        <strong class="{{ $warna }}">{{
                                            number_format($hasil)
                                        }}</strong>

                                        <span class="mx-2 text-zinc-400"
                                            >|</span
                                        >

                                        <span class="font-medium"
                                            >Selisih:</span
                                        >
                                        <strong class="{{ $warna }}"
                                            >{{ $tanda
                                            }}{{
                                                number_format(abs($selisih))
                                            }}</strong
                                        >

                                        <span class="mx-2 text-zinc-400"
                                            >|</span
                                        >

                                        <span class="text-xs">
                                            Tanggal:
                                            {{ $first["tanggal"] ?? "-" }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @empty

        {{-- JIKA TIDAK ADA DATA --}}
        <div class="text-center p-12 text-zinc-500 dark:text-zinc-400">
            <p class="text-lg">Tidak ada data produksi untuk tanggal ini.</p>
        </div>

        @endforelse
    </div>
</x-filament-panels::page>
