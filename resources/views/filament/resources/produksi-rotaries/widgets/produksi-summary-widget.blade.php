<x-filament::widget>
    <x-filament::card class="w-full space-y-10">
        {{-- ================= TOTAL PRODUKSI ================= --}}
        <div class="text-center">
            <div class="text-4xl font-extrabold text-primary">
                {{ number_format($summary["totalAll"] ?? 0) }}
            </div>
            <div class="text-sm text-gray-500">Total Produksi (Lembar)</div>
        </div>

        {{-- ================= REKAP PER KW ================= --}}
        <div>
            <div class="mb-3 font-semibold text-lg">Rekap per KW</div>

            <div class="flex flex-wrap gap-4">
                @foreach ($summary['perKw'] ?? [] as $row)
                <div
                    class="w-[120px] rounded-xl border bg-white p-4 text-center shadow-sm"
                >
                    <div class="text-xs text-gray-500">KW {{ $row->kw }}</div>
                    <div class="text-xl font-bold text-primary">
                        {{ number_format($row->total) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ================= REKAP PER JENIS KAYU ================= --}}
        <div>
            <div class="mb-3 font-semibold text-lg">Rekap per Jenis Kayu</div>

            @php $jenisGrouped = []; foreach ($summary['perJenisKayuKw'] ?? []
            as $row) { $jenisGrouped[$row->jenis_kayu][] = $row; } @endphp

            <div class="flex flex-wrap gap-5">
                @foreach ($jenisGrouped as $jenisKayu => $items) @php
                $totalJenis = 0; foreach ($items as $i) { $totalJenis +=
                $i->total; } @endphp

                <div
                    class="w-[260px] rounded-xl border bg-white p-4 shadow-sm space-y-3"
                >
                    <div class="flex justify-between items-center">
                        <div class="font-semibold">
                            {{ $jenisKayu }}
                        </div>
                        <div class="font-bold text-primary">
                            {{ number_format($totalJenis) }}
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach ($items as $row)
                        <div
                            class="min-w-[80px] rounded-lg bg-gray-100 px-3 py-2 text-center"
                        >
                            <div class="text-xs text-gray-500">
                                KW {{ $row->kw }}
                            </div>
                            <div class="font-semibold text-sm">
                                {{ number_format($row->total) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ================= REKAP PRODUKSI PER LAHAN ================= --}}
        <div>
            <div class="mb-4 font-semibold text-lg">
                Rekap Produksi per Lahan
            </div>

            @php $lahanGrouped = []; foreach ($summary['perLahanJenisKayuKw'] ??
            [] as $row) { $lahanGrouped[$row->lahan_id][] = $row; } @endphp

            <div class="flex flex-wrap gap-6">
                @foreach ($lahanGrouped as $itemsLahan) @php $first =
                $itemsLahan[0]; $totalLahan = 0; foreach ($itemsLahan as $r) {
                $totalLahan += $r->total; } $jenisInLahan = []; foreach
                ($itemsLahan as $r) { $jenisInLahan[$r->jenis_kayu][] = $r; }
                @endphp

                <div
                    class="w-[360px] rounded-2xl border bg-white p-6 shadow-sm space-y-4"
                >
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold">
                                {{ $first->kode_lahan }} -
                                {{ $first->nama_lahan }}
                            </div>
                            <div class="text-xs text-gray-500">Total Lahan</div>
                        </div>
                        <div class="text-2xl font-bold text-primary">
                            {{ number_format($totalLahan) }}
                        </div>
                    </div>

                    @foreach ($jenisInLahan as $jenisKayu => $rows)
                    <div>
                        <div class="mb-2 text-sm font-medium">
                            {{ $jenisKayu }}
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($rows as $row)
                            <div
                                class="min-w-[90px] rounded-xl bg-gray-100 px-4 py-2 text-center"
                            >
                                <div class="text-xs text-gray-500">
                                    KW {{ $row->kw }}
                                </div>
                                <div class="font-semibold">
                                    {{ number_format($row->total) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>

<!-- <x-filament::widget>
    <x-filament::card class="w-full">
        <div class="space-y-8 w-full">
            {{-- TOTAL --}}
            <div class="text-center">
                <div class="text-4xl font-extrabold text-primary">
                    {{ number_format($summary["totalAll"] ?? 0) }}
                </div>
                <div class="text-sm text-gray-500">Total Produksi (Lembar)</div>
            </div>

            {{-- KW --}}
           <div>
    <div class="mb-3 font-semibold">Rekap per KW</div>

    {{-- GRID KW AUTO WIDTH --}}
    <div
        class="grid gap-4"
        style="grid-template-columns: repeat(auto-fit, minmax(120px, max-content));"
    >
        @foreach($summary['perKw'] ?? [] as $kw)
            <div
                class="rounded-xl p-4 text-center bg-white shadow-sm border"
            >
                <div class="text-xs text-gray-500">
                    KW {{ $kw->kw }}
                </div>

                <div class="text-xl font-bold text-primary">
                    {{ number_format($kw->total) }}
                </div>
            </div>
        @endforeach
    </div>
</div>
{{-- REKAP PER JENIS KAYU / KW --}}
<div>
    <div class="mb-3 font-semibold">
        Rekap per Jenis Kayu
    </div>

    @php
        $grouped = collect($summary['perJenisKayuKw'] ?? [])
            ->groupBy('jenis_kayu');
    @endphp

    <div
        class="grid gap-4"
        style="grid-template-columns: repeat(auto-fit, minmax(220px, max-content));"
    >
        @foreach ($grouped as $jenisKayu => $items)
            @php
                $totalJenis = $items->sum('total');
            @endphp

            <div class="rounded-xl border bg-white p-4 shadow-sm space-y-3">

                {{-- HEADER --}}
                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-900">
                        {{ $jenisKayu }}
                    </div>

                    <div class="text-sm font-bold text-primary">
                        {{ number_format($totalJenis) }}
                    </div>
                </div>

                {{-- KW --}}
                <div class="flex flex-wrap gap-2">
                    @foreach ($items as $row)
                        <div
                            class="px-3 py-2 rounded-lg bg-gray-100 text-center min-w-[72px]"
                        >
                            <div class="text-[10px] text-gray-500 uppercase">
                                KW {{ $row->kw }}
                            </div>
                            <div class="text-sm font-semibold">
                                {{ number_format($row->total) }}
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
    </div>
</div>


   
     {{-- REKAP PRODUKSI PER LAHAN --}}
<div>
    <div class="mb-4 font-semibold text-lg">
        Rekap Produksi per Lahan
    </div>

    @php
        $grouped = collect($summary['perLahanJenisKayuKw'] ?? [])
            ->groupBy('lahan_id');
    @endphp

    {{-- ⬇️ GRID AUTO-WRAP --}}
    <div
        class="grid gap-6"
        style="grid-template-columns: repeat(auto-fit, minmax(320px, max-content));"
    >
        @foreach ($grouped as $itemsLahan)
            @php
                $totalLahan = $itemsLahan->sum('total');
                $first = $itemsLahan->first();
            @endphp

            {{-- CARD --}}
            <div class="rounded-2xl bg-white shadow-sm border p-6 space-y-4">
                {{-- HEADER --}}
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-semibold">
                            {{ $first->kode_lahan }} - {{ $first->nama_lahan }}
                        </div>
                        <div class="text-xs text-gray-500">
                            Total Lahan
                        </div>
                    </div>

                    <div class="text-2xl font-bold text-primary">
                        {{ number_format($totalLahan) }}
                    </div>
                </div>

                {{-- JENIS KAYU --}}
                @foreach ($itemsLahan->groupBy('jenis_kayu') as $jenisKayu => $itemsJenis)
                    <div>
                        <div class="text-sm font-medium mb-2">
                            {{ $jenisKayu }}
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @foreach ($itemsJenis as $row)
                                <div
                                    class="px-4 py-2 rounded-xl bg-gray-100 text-center min-w-[90px]"
                                >
                                    <div class="text-xs text-gray-500">
                                        KW {{ $row->kw }}
                                    </div>
                                    <div class="font-semibold">
                                        {{ number_format($row->total) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

    </x-filament::card>
</x-filament::widget> -->

<!-- {{-- LAHAN --}}
            <div>
                <div class="mb-3 font-semibold">Rekap per Lahan</div>

                {{-- GRID LAHAN --}}
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                >
                    @foreach($summary['perLahan'] ?? [] as $lahan)
                    <div
                        class="border rounded-xl p-4 shadow-sm text-center bg-white"
                    >
                        {{-- Kode + Nama --}}
                        <div class="text-sm text-gray-500">
                            {{ $lahan->kode_lahan }} - {{ $lahan->nama_lahan }}
                        </div>

                        {{-- TOTAL --}}
                        <div class="text-xl font-bold text-primary mt-1">
                            {{ number_format($lahan->total) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{-- LAHAN - JENIS KAYU - KW --}}
        <div>
            <div class="mb-3 font-semibold">
                Rekap per Lahan / Jenis Kayu / KW
            </div>

            @php $grouped = collect($summary['perLahanJenisKayuKw'] ?? [])
            ->groupBy('kode_lahan'); @endphp

            <div class="space-y-6">
                @foreach ($grouped as $kodeLahan => $itemsLahan)
                <div class="border rounded-xl p-4 bg-gray-50">
                    {{-- HEADER LAHAN --}}
                    <div class="font-semibold text-primary mb-3">
                        Lahan {{ $kodeLahan }} -
                        {{ $itemsLahan->first()->nama_lahan }}
                    </div>

                    @foreach ($itemsLahan->groupBy('jenis_kayu') as $jenisKayu
                    => $itemsJenis)
                    <div class="mb-4">
                        {{-- JENIS KAYU --}}
                        <div class="text-sm font-medium text-gray-700 mb-2">
                            {{ $jenisKayu }}
                        </div>

                        {{-- KW GRID --}}
                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"
                        >
                            @foreach ($itemsJenis as $row)
                            <div
                                class="border rounded-lg p-3 text-center bg-white shadow-sm"
                            >
                                <div class="text-xs text-gray-500">
                                    KW {{ $row->kw }}
                                </div>

                                <div class="font-bold text-primary">
                                    {{ number_format($row->total) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div> -->
