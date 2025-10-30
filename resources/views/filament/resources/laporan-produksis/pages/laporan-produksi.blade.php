<x-filament-panels::page>
    <div class="flex justify-end mb-4">
        <x-filament::button
            wire:click="downloadLaporan"
            icon="heroicon-o-arrow-down-tray"
        >
            Download Laporan (CSV)
        </x-filament::button>
    </div>

    <div
        class="overflow-x-auto rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"
    >
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th
                        class="px-4 py-2 text-left font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Tanggal Produksi
                    </th>
                    <th
                        class="px-4 py-2 text-left font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Mesin
                    </th>
                    <th
                        class="px-4 py-2 text-left font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Kendala
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($this->produksi as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-4 py-2 text-gray-800 dark:text-gray-200">
                        {{ $item->tgl_produksi }}
                    </td>
                    <td class="px-4 py-2 text-gray-800 dark:text-gray-200">
                        {{ $item->mesin->nama_mesin ?? '-' }}
                    </td>
                    <td class="px-4 py-2 text-gray-800 dark:text-gray-200">
                        {{ $item->kendala ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
