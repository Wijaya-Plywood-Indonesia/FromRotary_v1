<x-filament-panels::page>
    @php $dataProduksi = $dataProduksi ?? []; $produksi = $dataProduksi[0] ??
    []; $bahan = $produksi['bahan'] ?? []; $hasil = $produksi['hasil'] ?? [];
    $pekerja = $produksi['pekerja'] ?? []; $kendala = $produksi['kendala'] ??
    'Tidak ada kendala.'; $maxRows = max( is_array($bahan) ? count($bahan) : 0,
    collect($hasil)->sum(fn($kwList) => is_array($kwList) ? count($kwList) : 0),
    is_array($pekerja) ? count($pekerja) : 0, 1 ); $hasilFlat = collect(); if
    (is_array($hasil)) { foreach ($hasil as $ukuran => $kwList) { foreach
    ($kwList ?? [] as $kw => $item) { if (is_array($item)) { $hasilFlat->push([
    'ukuran' => $ukuran, 'kw' => $kw, 'palet' => $item['palet'] ?? 0, 'lembar'
    => $item['lembar'] ?? 0, ]); } } } } @endphp

    <!-- SCROLL HORIZONTAL -->
    <div
        class="w-full overflow-x-auto rounded-xl shadow-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700"
    >
        <div class="min-w-[1400px] p-4">
            <table
                class="w-full text-sm border-collapse border border-zinc-300 dark:border-zinc-600"
            >
                <thead>
                    <!-- Judul -->
                    <tr>
                        <th
                            colspan="17"
                            class="p-4 text-xl font-bold text-center bg-zinc-700 text-white"
                        >
                            LAPORAN PRODUKSI ROTARY
                        </th>
                    </tr>

                    <!-- Header utama -->
                    <tr class="border-t-4 border-zinc-700 dark:border-zinc-700">
                        <th
                            colspan="2"
                            class="p-3 text-center font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-r-2 border-zinc-300 dark:border-zinc-600"
                        >
                            BAHAN
                        </th>
                        <th
                            colspan="8"
                            class="p-3 text-center font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-r-2 border-zinc-300 dark:border-zinc-600"
                        >
                            HASIL PRODUKSI
                        </th>
                        <th
                            colspan="6"
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

                    <!-- Sub header -->
                    <tr
                        class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300 border-t border-zinc-300 dark:border-zinc-600"
                    >
                        <th class="p-2 text-left text-xs font-medium w-28">
                            Lahan
                        </th>
                        <th
                            class="p-2 text-right text-xs font-medium w-16 border-r-2 border-zinc-300 dark:border-zinc-600"
                        >
                            Batang
                        </th>

                        <th class="p-2 text-center text-xs w-20">Ukuran</th>
                        <th class="p-2 text-center text-xs w-12">KW</th>
                        <th class="p-2 text-center text-xs w-16">Palet</th>
                        <th class="p-2 text-center text-xs w-16">Lembar</th>
                        <th class="p-2 text-center text-xs w-20">Ukuran</th>
                        <th class="p-2 text-center text-xs w-12">KW</th>
                        <th class="p-2 text-center text-xs w-16">Palet</th>
                        <th
                            class="p-2 text-center text-xs w-16 border-r-2 border-zinc-300 dark:border-zinc-600"
                        >
                            Lembar
                        </th>

                        <th class="p-2 text-center text-xs w-24">Nama</th>
                        <th class="p-2 text-center text-xs w-16">Masuk</th>
                        <th class="p-2 text-center text-xs w-16">Pulang</th>
                        <th class="p-2 text-center text-xs w-16">Ijin</th>
                        <th class="p-2 text-center text-xs w-16">Target</th>
                        <th
                            class="p-2 text-center text-xs border-r-2 border-zinc-300 dark:border-zinc-600 w-20"
                        >
                            Ket
                        </th>

                        <th class="p-2 text-center text-xs w-[24rem]"></th>
                    </tr>
                </thead>

                <tbody>
                    @if ($maxRows === 0)
                    <tr>
                        <td
                            colspan="17"
                            class="p-8 text-center text-zinc-500 dark:text-zinc-400 italic text-lg"
                        >
                            Tidak ada data produksi.
                        </td>
                    </tr>
                    @else @php $rowIndex = 0; @endphp @for ($i = 0; $i <
                    $maxRows; $i++) @php $items = $hasilFlat->skip($i *
                    2)->take(2); $item1 = $items->get(0); $item2 =
                    $items->get(1); @endphp

                    <tr
                        class="{{ $rowIndex++ % 2 === 1 ? 'bg-zinc-50 dark:bg-zinc-800/50' : 'bg-white dark:bg-zinc-900' }} border-t border-zinc-300 dark:border-zinc-700"
                    >
                        {{-- BAHAN --}}
                        <td
                            class="p-2 text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $bahan[$i]["lahan"] ?? "" }}
                        </td>
                        <td
                            class="p-2 text-right text-xs border-r-2 border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-zinc-100"
                        >
                            {{ $bahan[$i]["batang"] ?? "" }}
                        </td>

                        {{-- HASIL PRODUKSI --}}
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item1["ukuran"] ?? "" }}
                        </td>
                        <td
                            class="p-2 text-center text-xs text-blue-600 dark:text-cyan-400"
                        >
                            {{ $item1 ? "KW ".$item1["kw"] : "" }}
                        </td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item1["palet"] ?? "" }}
                        </td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item1["lembar"] ?? "" }}
                        </td>

                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item2["ukuran"] ?? "" }}
                        </td>
                        <td
                            class="p-2 text-center text-xs text-blue-600 dark:text-cyan-400"
                        >
                            {{ $item2 ? "KW ".$item2["kw"] : "" }}
                        </td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item2["palet"] ?? "" }}
                        </td>
                        <td
                            class="p-2 text-center text-xs border-r-2 border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item2["lembar"] ?? "" }}
                        </td>

                        {{-- PEKERJA --}}
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

                        {{-- KENDALA --}}
                        @if ($i === 0)
                        <td
                            class="p-3 align-top bg-zinc-50 dark:bg-zinc-800"
                            rowspan="{{ $maxRows }}"
                        >
                            <div class="text-left h-full">
                                <div
                                    class="text-xs text-zinc-800 dark:text-zinc-300 whitespace-pre-line leading-snug max-h-64 overflow-y-auto align-top text-left"
                                >
                                    {{ $kendala }}
                                </div>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endfor @endif
                </tbody>

                <tfoot
                    class="bg-zinc-100 dark:bg-zinc-800 border-t-2 border-zinc-300 dark:border-zinc-600"
                >
                    <tr>
                        <td
                            colspan="17"
                            class="p-3 text-center text-zinc-600 dark:text-zinc-400 text-xs"
                        >
                            Total Data:
                            {{
                                is_array($dataProduksi)
                                    ? count($dataProduksi)
                                    : 0
                            }}
                            hari | Terakhir diperbarui:
                            {{ now()->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament-panels::page>
