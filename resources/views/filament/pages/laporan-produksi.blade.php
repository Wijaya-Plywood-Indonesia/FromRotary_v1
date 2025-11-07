<x-filament-panels::page>
    @php $dataProduksi = $dataProduksi ?? []; $groupedByMesin =
    collect($dataProduksi)->groupBy('mesin'); $totalBg = 'bg-sky-50
    dark:bg-sky-900/20'; $totalCellBg = 'bg-sky-100 dark:bg-sky-800'; $statusBg
    = 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700
    dark:text-emerald-300'; $pekerjaBg = 'bg-yellow-50 dark:bg-yellow-900/20
    text-yellow-700 dark:text-yellow-300'; @endphp

    <div class="space-y-12">
        @forelse ($groupedByMesin as $mesinNama => $produksiList) @php
        $firstProduksi = $produksiList->first(); $bahan =
        $firstProduksi['bahan'] ?? []; $hasil = $firstProduksi['hasil'] ?? [];
        $pekerja = $firstProduksi['pekerja'] ?? []; $kendala =
        $firstProduksi['kendala'] ?? 'Tidak ada kendala.'; $hasilFlat =
        collect(); $totalLembarHarian = 0; $totalM3Harian = 0.0; foreach ($hasil
        as $key => $item) { if ($key === 'TOTAL_SEMUA_KW') { $totalLembarHarian
        = $item['lembar']; $totalM3Harian = $item['total_m3']; continue; }
        $hasilFlat->push($item); } $maxRows = max( is_array($bahan) ?
        count($bahan) : 0, ceil($hasilFlat->count() / 2), is_array($pekerja) ?
        count($pekerja) : 0, 1 ); $totalBatang = $produksiList->sum(fn($p) =>
        (int) ($p['summary']['total_batang'] ?? 0)); $totalLembar =
        $produksiList->sum(fn($p) => (int) ($p['summary']['total_lembar'] ??
        0)); $totalM3 = $produksiList->sum(fn($p) => (float)
        ($p['summary']['total_m3'] ?? 0)); $totalJamKerja =
        $produksiList->sum(fn($p) => (int) ($p['summary']['jam_kerja'] ?? 0));
        $totalTarget = $produksiList->sum(fn($p) =>
        collect($p['pekerja'])->sum(fn($pe) => (float) ($pe['pot_target'] ??
        0))); $totalStatus = $totalLembar; $totalPekerja =
        $produksiList->sum(fn($p) => count($p['pekerja'])); @endphp

        <div
            class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
        >
            <div
                class="bg-gradient-to-r from-zinc-700 to-zinc-800 p-4 text-white"
            >
                <h2 class="text-lg font-bold text-center">
                    MESIN: {{ strtoupper($mesinNama) }}
                </h2>
            </div>

            <div class="p-4">
                <div class="w-full overflow-x-auto">
                    <div class="min-w-[1600px]">
                        <table
                            class="w-full text-sm border-collapse border border-zinc-300 dark:border-zinc-600"
                        >
                            <thead>
                                <tr>
                                    <th
                                        colspan="19"
                                        class="p-4 text-xl font-bold text-center bg-zinc-700 text-white"
                                    >
                                        LAPORAN PRODUKSI ROTARY
                                    </th>
                                </tr>

                                <tr class="border-t-4 border-zinc-700">
                                    <th
                                        colspan="2"
                                        class="p-3 text-center font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-r-2 border-zinc-300 dark:border-zinc-600"
                                    >
                                        BAHAN
                                    </th>
                                    <th
                                        colspan="9"
                                        class="p-3 text-center font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-r-2 border-zinc-300 dark:border-zinc-600"
                                    >
                                        HASIL PRODUKSI
                                    </th>
                                    <th
                                        colspan="7"
                                        class="p-3 text-center font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-r-2 border-zinc-300 dark:border-zinc-600"
                                    >
                                        DATA PEKERJA
                                    </th>
                                    <th
                                        class="p-3 text-center font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                                    >
                                        KENDALA
                                    </th>
                                </tr>

                                <tr
                                    class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300 border-t border-zinc-300 dark:border-zinc-600"
                                >
                                    <th
                                        class="p-2 text-left text-xs font-medium w-28"
                                    >
                                        Lahan
                                    </th>
                                    <th
                                        class="p-2 text-right text-xs font-medium w-16 border-r-2 border-zinc-300 dark:border-zinc-600"
                                    >
                                        Batang
                                    </th>
                                    <th class="p-2 text-center text-xs w-20">
                                        Ukuran
                                    </th>
                                    <th class="p-2 text-center text-xs w-12">
                                        Kualitas
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Jenis
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Total
                                    </th>
                                    <th class="p-2 text-center text-xs w-20">
                                        m3
                                    </th>
                                    <th class="p-2 text-center text-xs w-12">
                                        Jam Kerja
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Target
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Status
                                    </th>
                                    <th
                                        class="p-2 text-center text-xs w-16 border-r-2 border-zinc-300 dark:border-zinc-600"
                                    >
                                        Pot Target
                                    </th>
                                    <th class="p-2 text-center text-xs w-24">
                                        ID
                                    </th>
                                    <th class="p-2 text-center text-xs w-24">
                                        Nama
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Masuk
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Pulang
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Ijin
                                    </th>
                                    <th class="p-2 text-center text-xs w-16">
                                        Target
                                    </th>
                                    <th
                                        class="p-2 text-center text-xs border-r-2 border-zinc-300 dark:border-zinc-600 w-20"
                                    >
                                        Ket
                                    </th>
                                    <th
                                        class="p-2 text-center text-xs w-[24rem]"
                                    ></th>
                                </tr>
                            </thead>

                            <tbody>
                                @for ($i = 0; $i < $maxRows; $i++) @php $item1 =
                                $hasilFlat->get($i * 2); $item2 =
                                $hasilFlat->get($i * 2 + 1); $showM3Item1 =
                                $item1 && (!$item2 || empty($item2['ukuran']));
                                $showM3Item2 = $item2 &&
                                empty($item1['ukuran']); @endphp

                                <tr
                                    class="{{
                                        $i % 2 === 1
                                            ? 'bg-zinc-50 dark:bg-zinc-800/50'
                                            : 'bg-white dark:bg-zinc-900'
                                    }} border-t border-zinc-300 dark:border-zinc-700"
                                >
                                    <td
                                        class="p-2 text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $bahan[$i]["lahan"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-right text-xs border-r-2 border-zinc-300 dark:border-zinc-600 font-medium text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $bahan[$i]["batang"] ?? "" }}
                                    </td>

                                    <td
                                        class="p-2 text-center text-xs font-medium text-blue-600 dark:text-cyan-400"
                                    >
                                        {{ $item1["ukuran"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-blue-600 dark:text-cyan-400"
                                    >
                                        {{ $item1["kw"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs font-medium text-amber-600 dark:text-amber-400"
                                    >
                                        {{ $item1["jenis_kayu"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs font-medium"
                                    >
                                        {{ $item1["lembar"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-green-600 dark:text-green-400 font-medium"
                                    >
                                        @if ($showM3Item1)
                                        {{
                                            number_format($item1["total_m3"], 3)
                                        }}
                                        @endif
                                    </td>

                                    <td
                                        class="p-2 text-center text-xs font-medium text-blue-600 dark:text-cyan-400"
                                    >
                                        {{ $item2["ukuran"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-blue-600 dark:text-cyan-400"
                                    >
                                        {{ $item2["kw"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs font-medium text-amber-600 dark:text-amber-400"
                                    >
                                        {{ $item2["jenis_kayu"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs font-medium border-r-2 border-zinc-300 dark:border-zinc-600"
                                    >
                                        @if ($showM3Item2)
                                        {{
                                            number_format($item2["total_m3"], 3)
                                        }}
                                        @endif
                                    </td>

                                    <td
                                        class="p-2 text-center text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $pekerja[$i]["id"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $pekerja[$i]["nama"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $pekerja[$i]["jam_masuk"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $pekerja[$i]["jam_pulang"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-yellow-600 dark:text-yellow-400"
                                    >
                                        {{ $pekerja[$i]["ijin"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $pekerja[$i]["pot_target"] ?? "" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r-2 border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $pekerja[$i]["keterangan"] ?? "" }}
                                    </td>

                                    @if ($i === 0)
                                    <td
                                        class="p-3 align-top bg-zinc-50 dark:bg-zinc-800"
                                        rowspan="{{ $maxRows }}"
                                    >
                                        <div class="text-left h-full">
                                            <div
                                                class="text-xs text-zinc-800 dark:text-zinc-300 whitespace-pre-line leading-snug max-h-64 overflow-y-auto"
                                            >
                                                {{ $kendala }}
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endfor

                                <!-- BARIS TOTAL OTOMATIS -->
                                <tr
                                    class="{{
                                        $totalBg
                                    }} font-bold text-zinc-800 dark:text-zinc-200"
                                >
                                    <td
                                        colspan="2"
                                        class="p-2 text-right {{
                                            $totalCellBg
                                        }}"
                                    >
                                        Total
                                    </td>
                                    <td class="{{ $totalCellBg }}"></td>
                                    <td class="{{ $totalCellBg }}"></td>
                                    <td class="{{ $totalCellBg }}"></td>
                                    <td class="{{ $totalCellBg }} text-center">
                                        {{ number_format($this->summary['total_lembar']) }}
                                    </td>
                                    <td class="{{ $totalCellBg }} text-center">
                                        {{ number_format($this->summary['total_m3'], 4) }}
                                    </td>
                                    <td class="{{ $totalCellBg }} text-center">
                                        {{ $this->summary['total_jam_kerja'] }}
                                    </td>
                                    <td class="{{ $totalCellBg }} text-center">
                                        {{ number_format($this->summary['total_target'], 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="{{
                                            $statusBg
                                        }} text-center font-bold"
                                    >
                                        {{ number_format($this->summary['total_status'], 0, ',', '.') }}
                                    </td>
                                    <td class="{{ $totalCellBg }} text-center">
                                        -
                                    </td>
                                    <td
                                        colspan="3"
                                        class="{{
                                            $pekerjaBg
                                        }} text-center font-medium"
                                    >
                                        Pekerja
                                    </td>
                                    <td
                                        class="{{
                                            $pekerjaBg
                                        }} text-center font-bold"
                                    >
                                        {{ $this->summary['total_pekerja'] }}
                                    </td>
                                    <td class="{{ $totalCellBg }}"></td>
                                </tr>
                            </tbody>

                            <tfoot
                                class="bg-zinc-100 dark:bg-zinc-800 border-t-2 border-zinc-300 dark:border-zinc-600"
                            >
                                <tr>
                                    <td
                                        colspan="19"
                                        class="p-3 text-center text-zinc-600 dark:text-zinc-400 text-xs"
                                    >
                                        Total Data:
                                        {{ $produksiList->count() }} hari |
                                        Terakhir diperbarui:
                                        {{ now()->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center p-12 text-zinc-500 dark:text-zinc-400">
            <p class="text-lg">Tidak ada data produksi.</p>
        </div>
        @endforelse
    </div>
</x-filament-panels::page>
