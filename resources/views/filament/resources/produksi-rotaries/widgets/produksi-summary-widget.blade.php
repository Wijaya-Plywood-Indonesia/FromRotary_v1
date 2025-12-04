<x-filament::widget>
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

                {{-- GRID KW - selalu berjajar ke samping --}}
                <div
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
                >
                    @foreach($summary['perKw'] ?? [] as $kw)
                    <div
                        class="border rounded-xl p-4 shadow-sm text-center bg-white"
                    >
                        <div class="text-sm text-gray-500">
                            KW {{ $kw->kw }}
                        </div>

                        <div class="text-xl font-bold text-primary">
                            {{ number_format($kw->total) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- LAHAN --}}
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
    </x-filament::card>
</x-filament::widget>
