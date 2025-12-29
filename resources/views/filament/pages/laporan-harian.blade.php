<x-filament-panels::page>
    <div class="p-4 bg-white dark:bg-zinc-900 rounded-lg shadow">
        {{ $this->form }}
    </div>

    @if($isLoading)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-white bg-opacity-75 dark:bg-zinc-900 dark:bg-opacity-75"
    >
        <div class="flex items-center space-x-3">
            <x-filament::loading-indicator class="w-8 h-8 text-primary-600" />
            <span class="text-lg font-medium text-zinc-700 dark:text-zinc-300"
                >Memuat data...</span
            >
        </div>
    </div>
    @endif @php $laporanGabungan = $laporanGabungan ?? []; $statistics =
    $statistics ?? []; $groupedByDivisi =
    collect($laporanGabungan)->groupBy('hasil'); @endphp

    <div class="space-y-12 mt-6">
        @forelse ($groupedByDivisi as $divisiNama => $pegawaiList) @php
        $totalPekerja = count($pegawaiList); $totalPotongan =
        $pegawaiList->sum('potongan_targ'); $firstPegawai =
        $pegawaiList->first(); $tanggal = $data['tanggal'] ??
        now()->format('Y-m-d'); $tanggalFormatted =
        \Carbon\Carbon::parse($tanggal)->format('d/m/Y'); @endphp

        <div
            class="bg-white dark:bg-zinc-900 rounded-sm shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
        >
            <div class="bg-zinc-800 p-4 text-white">
                <h2 class="text-lg font-bold text-center">
                    {{ strtoupper($divisiNama) }}
                </h2>
            </div>

            <div class="p-4">
                <div class="w-full overflow-x-auto">
                    <div class="min-w-[800px]">
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
                                    class="bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-300 border-t border-zinc-300 dark:border-zinc-600"
                                >
                                    <th
                                        class="p-2 text-center text-xs font-medium w-16"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="p-2 text-left text-xs font-medium w-40"
                                    >
                                        Nama
                                    </th>
                                    <th
                                        class="p-2 text-center text-xs font-medium w-20"
                                    >
                                        Masuk
                                    </th>
                                    <th
                                        class="p-2 text-center text-xs font-medium w-20"
                                    >
                                        Pulang
                                    </th>
                                    <th
                                        class="p-2 text-center text-xs font-medium w-16"
                                    >
                                        Ijin
                                    </th>
                                    <th
                                        class="p-2 text-right text-xs font-medium w-36"
                                    >
                                        Potongan Target
                                    </th>
                                    <th
                                        class="p-2 text-left text-xs font-medium"
                                    >
                                        Keterangan
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pegawaiList as $i => $p)
                                <tr
                                    class="{{
                                        $i % 2 === 1
                                            ? 'bg-zinc-50 dark:bg-zinc-800/50'
                                            : 'bg-white dark:bg-zinc-900'
                                    }} border-t border-zinc-300 dark:border-zinc-700"
                                >
                                    <td
                                        class="p-2 text-center text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $p["kodep"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-left text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 font-medium"
                                    >
                                        {{ $p["nama"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $p["masuk"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $p["pulang"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-center text-xs border-r border-zinc-300 dark:border-zinc-700 text-yellow-600 dark:text-yellow-400"
                                    >
                                        {{ $p["ijin"] ?? "-" }}
                                    </td>
                                    <td
                                        class="p-2 text-right text-xs border-r border-zinc-300 dark:border-zinc-700 font-bold {{
                                            isset($p['potongan_targ']) &&
                                            $p['potongan_targ'] > 0
                                                ? 'text-red-600 dark:text-red-400'
                                                : 'text-zinc-700'
                                        }}"
                                    >
                                        @if(isset($p['potongan_targ']) &&
                                        $p['potongan_targ'] > 0) Rp
                                        {{
                                            number_format(
                                                $p["potongan_targ"],
                                                0,
                                                ",",
                                                "."
                                            )
                                        }}
                                        @else - @endif
                                    </td>
                                    <td
                                        class="p-2 text-left text-xs text-zinc-700 dark:text-zinc-300"
                                    >
                                        {{ $p["keterangan"] ?? "-" }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot
                                class="bg-zinc-100 dark:bg-zinc-800 border-t-2 border-zinc-300 dark:border-zinc-600"
                            >
                                <tr>
                                    <td
                                        colspan="7"
                                        class="p-3 text-center text-xs text-zinc-600 dark:text-zinc-400 space-x-3"
                                    >
                                        <span class="font-medium"
                                            >Total Pekerja:</span
                                        >
                                        <strong>{{ $totalPekerja }}</strong>
                                        <span class="text-zinc-400">|</span>
                                        <span class="font-medium"
                                            >Total Potongan:</span
                                        >
                                        <strong
                                            class="font-mono text-red-600 dark:text-red-400"
                                        >
                                            Rp
                                            {{
                                                number_format(
                                                    $totalPotongan,
                                                    0,
                                                    ",",
                                                    "."
                                                )
                                            }}
                                        </strong>
                                        <span class="text-zinc-400">|</span>
                                        <span class="text-xs"
                                            >Tanggal:
                                            {{ $tanggalFormatted }}</span
                                        >
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
            <p class="text-lg">Tidak ada data untuk tanggal ini.</p>
        </div>
        @endforelse
    </div>

    @if(!empty($laporanGabungan))
    <div
        class="mt-8 bg-white dark:bg-zinc-900 rounded-sm shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden"
    >
        <div class="bg-zinc-800 p-4 text-white">
            <h2 class="text-lg font-bold text-center">RINGKASAN KESELURUHAN</h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div
                    class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                >
                    <div
                        class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                    >
                        {{ $statistics["rotary"] ?? 0 }}
                    </div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Rotary
                    </div>
                </div>

                <div
                    class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800"
                >
                    <div
                        class="text-2xl font-bold text-green-600 dark:text-green-400"
                    >
                        {{ $statistics["repair"] ?? 0 }}
                    </div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Repair
                    </div>
                </div>

                <div
                    class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800"
                >
                    <div
                        class="text-2xl font-bold text-yellow-600 dark:text-yellow-400"
                    >
                        {{ $statistics["dryer"] ?? 0 }}
                    </div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Dryer
                    </div>
                </div>

                <div
                    class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800"
                >
                    <div
                        class="text-2xl font-bold text-purple-600 dark:text-purple-400"
                    >
                        {{ $statistics["kedi"] ?? 0 }}
                    </div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Kedi
                    </div>
                </div>

                <div
                    class="text-center p-4 bg-pink-50 dark:bg-pink-900/20 rounded-lg border border-pink-200 dark:border-pink-800"
                >
                    <div
                        class="text-2xl font-bold text-pink-600 dark:text-pink-400"
                    >
                        {{ $statistics["stik"] ?? 0 }}
                    </div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Stik
                    </div>
                </div>

                <div
                    class="text-center p-4 bg-zinc-50 dark:bg-zinc-900/20 rounded-lg border border-zinc-200 dark:border-zinc-800"
                >
                    <div
                        class="text-2xl font-bold text-zinc-800 dark:text-zinc-200"
                    >
                        {{ $statistics["total"] ?? 0 }}
                    </div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Total Pegawai
                    </div>
                </div>
            </div>

            <div
                class="mt-6 text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"
            >
                <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                    Total Potongan Seluruh Divisi
                </div>
                <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                    Rp
                    {{ number_format(collect($laporanGabungan)->sum('potongan_targ'), 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
