<div class="space-y-4">
    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        Detail Pekerja - Meja {{ $meja }}
    </div>

    @foreach ($details as $item)
    <div
        class="p-4 border rounded-lg shadow-sm bg-white border-gray-200 text-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
    >
        <div class="space-y-1">
            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300"
                    >Pegawai:</span
                >
                {{ $item->rencanaRepair->rencanaPegawai->pegawai->nama_pegawai }}
            </div>

            <div>
                <span class="font-semibold text-gray-700 dark:text-gray-300"
                    >Kode Pegawai:</span
                >
                {{ $item->rencanaRepair->rencanaPegawai->pegawai->kode_pegawai }}
            </div>
        </div>
    </div>
    @endforeach @if ($details->isEmpty())
    <p class="text-gray-500 dark:text-gray-400 text-sm">
        Tidak ada pegawai pada meja ini.
    </p>
    @endif
</div>
