<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, maximum-scale=1"
        />
        <title>Laporan Pembelian Kayu</title>

        <style>
            .phone-wrapper {
                width: 360px;
                margin: 0 auto;
                background: #fff;
                padding: 6px;
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                margin: 0;
                background: #e5e5e5;
                line-height: 1.05;
            }

            h3 {
                margin: 0 0 3px 0;
                font-size: 12px;
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 2px;
            }

            th,
            td {
                border: 1px solid #444;
                padding: 2px;
                text-align: right;
                vertical-align: middle;
            }

            .header-table td {
                border: none;
                padding: 1px;
                text-align: left;
            }

            .group-title {
                background: #eaeaea;
                font-weight: bold;
                padding: 2px;
                margin-top: 6px;
                border: 1px solid #444;
                font-size: 10px;
            }

            .signature td {
                border: none;
                padding: 2px;
                text-align: center;
            }

            .footer {
                font-size: 9px;
                text-align: right;
                margin-top: 6px;
            }

            /* Style Tally Marks agar rapi */
            .tally-wrapper {
                display: flex;
                flex-wrap: wrap;
                gap: 1px;
                justify-content: flex-start;
                min-width: 60px;
            }

            @media (max-width: 360px) {
                .phone-wrapper {
                    width: 100%;
                }
            }
        </style>
    </head>

    <body>
        <div class="phone-wrapper">
            <h3 style="text-align: center">NOTA KAYU</h3>

            <table class="header-table">
                <tr>
                    <td>No : {{ $record->no_nota }}</td>
                    <td>Seri : {{ $record->kayuMasuk->seri }}</td>
                    <td>{{ $record->kayuMasuk->tgl_kayu_masuk }}</td>
                </tr>
                <tr>
                    <td>
                        {{ $record->kayuMasuk->penggunaanSupplier->nama_supplier ?? '-' }}
                    </td>
                    <td>
                        {{ $record->kayuMasuk->penggunaanKendaraanSupplier->nopol_kendaraan ?? '-' }}
                    </td>
                    <td>
                        {{ $record->kayuMasuk->penggunaanDokumenKayu->dokumen_legal ?? '-' }}
                    </td>
                </tr>
            </table>

            @php $details = $record->kayuMasuk->detailTurusanKayus ?? collect();
            $grouped = $details->groupBy(function($item) { $kodeLahan =
            optional($item->lahan)->kode_lahan ?? '-'; $grade = $item->grade ??
            0; $panjang = $item->panjang ?? '-'; $jenis =
            optional($item->jenisKayu)->nama_kayu ?? '-'; return
            "{$kodeLahan}|{$grade}|{$panjang}|{$jenis}"; }); $grandBatang = 0;
            $grandM3 = 0; $grandHarga = 0; @endphp @foreach($grouped as $key =>
            $items) @php [$kodeLahan, $grade, $panjang, $jenis] = explode('|',
            $key); $gradeText = $grade == 1 ? 'A' : ($grade == 2 ? 'B' : '-');
            $subtotalBatang = $items->sum('kuantitas'); $subtotalM3 =
            $items->sum('kubikasi'); @endphp

            <div class="group-title">
                {{ $kodeLahan }} &nbsp;&nbsp; {{ $panjang }} cm {{ $jenis }} ({{
                    $gradeText
                }})
            </div>

            @php $firstItem = $items->first(); $idJenisKayu =
            optional($firstItem->jenisKayu)->id ?? $firstItem->id_jenis_kayu ??
            null; $groupedByDiameter =
            app(\App\Http\Controllers\NotaKayuController::class)
            ->groupByDiameterSpesifik($items, $idJenisKayu, $grade, $panjang);
            $subtotalHarga = $groupedByDiameter->sum('total_harga');
            $grandBatang += $subtotalBatang; $grandM3 += $subtotalM3;
            $grandHarga += $subtotalHarga; @endphp

            {{-- === Tabel Detail dengan Tally Marks === --}}
            <table border="1" cellspacing="0" cellpadding="5" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 10%">D (cm)</th>
                        <th style="text-align: left; width: 35%">Turus</th>
                        <th style="text-align: center; width: 10%">Btg</th>
                        <th style="text-align: center; width: 15%">m³</th>
                        <th style="text-align: center; width: 15%">Harga</th>
                        <th style="text-align: center; width: 15%">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedByDiameter as $detail)
                    <tr>
                        <!-- Diameter -->
                        <td style="text-align: center; font-weight: bold">
                            {{ $detail["diameter"] }}
                        </td>

                        <!-- Visual Tally Marks -->
                        <td style="text-align: left">
                            <div class="tally-wrapper">
                                @php $cnt = (int)$detail['batang']; $groups =
                                floor($cnt / 5); $rem = $cnt % 5; @endphp

                                {{-- Loop Kelompok 5 (4 tegak + 1 miring) --}}
                                @for($i = 0; $i < $groups; $i++)
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 50 50"
                                    style="
                                        stroke: #222;
                                        fill: none;
                                        stroke-width: 5px;
                                        stroke-linecap: round;
                                        stroke-linejoin: round;
                                    "
                                >
                                    <path d="M10 5 V45" />
                                    <path d="M20 5 V45" />
                                    <path d="M30 5 V45" />
                                    <path d="M40 5 V45" />
                                    <path
                                        d="M5 45 L45 5"
                                        style="stroke: #d00; opacity: 0.7"
                                    />
                                </svg>
                                @endfor

                                {{-- Loop Sisa --}}
                                @if($rem > 0)
                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 50 50"
                                    style="
                                        stroke: #222;
                                        fill: none;
                                        stroke-width: 5px;
                                        stroke-linecap: round;
                                    "
                                >
                                    @for($j = 1; $j <= $rem; $j++)
                                    <path d="M{{ $j * 10 }} 5 V45" />
                                    @endfor
                                </svg>
                                @endif
                            </div>
                        </td>

                        <!-- Jumlah Angka -->
                        <td style="text-align: center; font-weight: bold">
                            {{ $detail["batang"] }}
                        </td>

                        <!-- Kubikasi -->
                        <td style="text-align: right">
                            {{
                                number_format($detail["kubikasi"], 4, ",", ".")
                            }}
                        </td>

                        <!-- Harga Satuan -->
                        <td style="text-align: right">
                            {{
                                number_format(
                                    $detail["harga_satuan"],
                                    0,
                                    ",",
                                    "."
                                )
                            }}
                        </td>

                        <!-- Total Harga -->
                        <td style="text-align: right">
                            {{
                                number_format(
                                    $detail["total_harga"],
                                    0,
                                    ",",
                                    "."
                                )
                            }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center">
                            Tidak ada data
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @php $totalBatangGrup = $groupedByDiameter->sum('batang');
                $totalKubikasiGrup = $groupedByDiameter->sum('kubikasi');
                $totalHargaGrup = $groupedByDiameter->sum('total_harga');
                @endphp

                <tfoot>
                    <tr style="font-weight: bold; background: #f7f7f7">
                        <td colspan="2" style="text-align: center">Total</td>
                        <td style="text-align: center">
                            {{ number_format($totalBatangGrup, 0, ",", ".") }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format($totalKubikasiGrup, 4, ",", ".") }}
                        </td>
                        <td></td>
                        <td style="text-align: right">
                            {{ number_format($totalHargaGrup, 0, ",", ".") }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endforeach

            <div
                style="
                    margin-top: 20px;
                    display: flex;
                    justify-content: flex-end;
                "
            >
                <table
                    style="
                        border-collapse: collapse;
                        text-align: right;
                        min-width: 300px;
                        width: 100%;
                    "
                >
                    <tr>
                        <td style="border: 1px solid #000">Total Kubikasi</td>
                        <td style="border: 1px solid #000">
                            {{ number_format($grandM3, 4, ",", ".") }} m³
                        </td>
                        <td style="text-align: right; border: 1px solid #000">
                            Grand Total
                        </td>
                        <td style="border: 1px solid #000">
                            Rp. {{ number_format($grandHarga, 0, ",", ".") }}
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="
                                text-align: right;
                                padding: 4px 10px;
                                border: 1px solid #000;
                            "
                        >
                            Total Batang
                        </td>
                        <td style="padding: 4px 10px; border: 1px solid #000">
                            {{ number_format($grandBatang) }} Batang
                        </td>
                        <td></td>

                        @php $final = $hargaFinal ?? $totalAkhir ?? $grandHarga;
                        $selisih = $grandHarga - $final; @endphp

                        <td style="padding: 4px 10px; border: 1px solid #000">
                            Rp. {{ number_format($selisih, 0, ",", ".") }}
                        </td>
                    </tr>

                    <tr>
                        <td
                            colspan="4"
                            style="
                                text-align: right;
                                font-weight: bold;
                                font-size: 18px;
                                padding: 10px 12px;
                                border: 2px solid #000;
                                background: #f2f2f2;
                            "
                        >
                            Total Akhir: Rp.
                            {{ number_format($final, 0, ",", ".") }}
                        </td>
                    </tr>
                </table>
            </div>

            <table class="signature" style="width: 100%; margin-top: 10px">
                <tr>
                    <td>Penanggung Jawab Kayu</td>
                    <td>Grader Kayu</td>
                </tr>
                <tr>
                    <td style="height: 30px"></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        {{ $record->penanggung_jawab ?? '(...................)' }}
                    </td>
                    <td>{{ $record->penerima ?? '(...................)' }}</td>
                </tr>
            </table>

            <div class="footer">
                Dicetak pada: {{ now()->format('d-m-Y H:i') }}
            </div>
        </div>
    </body>
</html>
