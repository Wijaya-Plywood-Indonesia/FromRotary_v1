<x-filament-panels::page>
    @php $dataProduksi = $dataProduksi ?? []; @endphp @if(empty($dataProduksi))
    <div class="p-12 text-center bg-white dark:bg-zinc-900 rounded-lg shadow">
        <p class="text-zinc-500 dark:text-zinc-400 text-lg">
            Belum ada data produksi.
        </p>
    </div>
    @else @foreach($dataProduksi as $produksi) @php $bahan = $produksi['bahan']
    ?? []; $hasil = $produksi['hasil'] ?? []; $pekerja = $produksi['pekerja'] ??
    []; $kendala = $produksi['kendala'] ?? 'Tidak ada kendala.'; $hasilFlat =
    collect(); if (is_array($hasil)) { foreach ($hasil as $ukuran => $kwList) {
    foreach ($kwList ?? [] as $kw => $item) { if (is_array($item)) {
    $hasilFlat->push([ 'ukuran' => $ukuran, 'kw' => $kw, 'palet' =>
    $item['palet'] ?? 0, 'lembar' => $item['lembar'] ?? 0, ]); } } } } $maxRows
    = max( is_array($bahan) ? count($bahan) : 0, is_array($pekerja) ?
    count($pekerja) : 0, 1 ); $hasilIndex = 0; @endphp

    {{-- Header Info Produksi --}}
    <div
        class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800"
    >
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">
                    📅 {{ $produksi["tanggal"] ?? "-" }}
                </h3>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    Mesin: {{ $produksi["mesin"] ?? "-" }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-blue-600 dark:text-blue-400">
                    ID Produksi: #{{ $produksi["id"] ?? "-" }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tabel Produksi --}}
    <div
        class="w-full overflow-x-auto rounded-sm shadow-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 mb-8"
    >
        <div class="min-w-[3200px] p-4">
            <table
                class="w-full text-sm border border-zinc-300 dark:border-zinc-600"
                style="table-layout: auto; width: 3200px"
            >
                <colgroup>
                    <col style="width: 112px" />
                    <col style="width: 64px" />
                    <col style="width: 80px" />
                    <col style="width: 48px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 80px" />
                    <col style="width: 48px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 96px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 64px" />
                    <col style="width: 80px" />
                    <col />
                </colgroup>
                <thead>
                    <!-- Judul -->
                    <tr>
                        <th
                            colspan="26"
                            class="p-4 text-xl font-bold text-center bg-zinc-700 text-white"
                        >
                            LAPORAN PRODUKSI ROTARY
                        </th>
                    </tr>

                    <!-- Header Utama -->
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
                            colspan="8"
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

                    <!-- Sub Header -->
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
                        <th class="p-2 text-center text-xs w-12">Kualitas</th>
                        <th class="p-2 text-center text-xs w-16">Jenis</th>
                        <th class="p-2 text-center text-xs w-16">Total</th>
                        <th class="p-2 text-center text-xs w-16">m3</th>
                        <th class="p-2 text-center text-xs w-16">Jam Kerja</th>
                        <th class="p-2 text-center text-xs w-16">Target</th>
                        <th class="p-2 text-center text-xs w-16">Status</th>
                        <th
                            class="p-2 text-center text-xs w-16 border-r-2 border-zinc-300 dark:border-zinc-600"
                        >
                            Pot Target
                        </th>

                        <th class="p-2 text-center text-xs w-24">ID</th>
                        <th class="p-2 text-center text-xs w-16">Nama</th>
                        <th class="p-2 text-center text-xs w-16">Masuk</th>
                        <th class="p-2 text-center text-xs w-16">Pulang</th>
                        <th class="p-2 text-center text-xs w-16">Izin</th>
                        <th class="p-2 text-center text-xs w-16">
                            Pot Target/Orang
                        </th>
                        <th
                            class="p-2 text-center text-xs border-r-2 border-zinc-300 dark:border-zinc-600 w-20"
                        >
                            Ket
                        </th>

                        <th class="p-2 text-center text-xs w-[50rem]"></th>
                    </tr>
                </thead>

                <tbody>
                    @if ($maxRows === 1 && empty($bahan) && empty($pekerja))
                    <tr>
                        <td
                            colspan="26"
                            class="p-8 text-center text-zinc-500 dark:text-zinc-400 italic text-lg"
                        >
                            Tidak ada data produksi.
                        </td>
                    </tr>
                    @else @for ($i = 0; $i < $maxRows; $i++) @php $item1 =
                    $hasilFlat->get($hasilIndex++); $item2 =
                    $hasilFlat->get($hasilIndex++); @endphp

                    <tr
                        class="{{
                            $i % 2 === 1
                                ? 'bg-zinc-50 dark:bg-zinc-800/50'
                                : 'bg-white dark:bg-zinc-900'
                        }} border-t border-zinc-300 dark:border-zinc-700"
                    >
                        {{-- BAHAN (2 kolom) --}}
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

                        {{-- HASIL PRODUKSI (15 kolom) --}}
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
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $item2["lembar"] ?? "" }}
                        </td>

                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        ></td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        ></td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        ></td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        ></td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        ></td>
                        <td
                            class="p-2 text-center text-xs text-zinc-700 dark:text-zinc-300"
                        ></td>
                        <td
                            class="p-2 text-center text-xs border-r-2 border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300"
                        ></td>

                        {{-- DATA PEKERJA (8 kolom) --}}
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

                        {{-- KENDALA (1 kolom - hanya di baris pertama) --}}
                        @if ($i === 0)
                        <td
                            class="p-3 align-top bg-zinc-50 dark:bg-zinc-800"
                            rowspan="{{ $maxRows }}"
                        >
                            <div
                                class="text-xs text-zinc-800 dark:text-zinc-300 whitespace-pre-line leading-snug max-h-64 overflow-y-auto"
                            >
                                {{ $kendala }}
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
                            colspan="26"
                            class="p-3 text-center text-zinc-600 dark:text-zinc-400 text-xs"
                        >
                            Terakhir diperbarui:
                            {{ now()->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @endforeach

    {{-- Summary Info --}}
    <div class="mt-6 p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
        <p class="text-center text-zinc-600 dark:text-zinc-400 text-sm">
            Total Record:
            <span class="font-bold">{{ count($dataProduksi) }}</span> hari
            produksi
        </p>
    </div>

    @endif
</x-filament-panels::page>
