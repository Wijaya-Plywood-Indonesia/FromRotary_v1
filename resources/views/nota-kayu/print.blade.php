<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Laporan Pembelian Kayu</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 25px;
                color: #000;
                background: #fff;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 8px;
            }
            th,
            td {
                border: 1px solid #444;
                padding: 5px;
                text-align: right;
            }
            th {
                background: #f5f5f5;
            }
            .group-title {
                background: #eaeaea;
                font-weight: bold;
                padding: 5px;
                margin-top: 15px;
                border: 1px solid #444;
            }
            .header-table td {
                border: none;
                padding: 4px;
            }
            .signature td {
                border: none;
                text-align: center;
                padding: 5px;
            }
        </style>
    </head>
    <body>
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

        @php $details = $record->kayuMasuk->detailMasukanKayu ?? collect();
        $grouped = $details->groupBy(function($item) { $kodeLahan =
        optional($item->lahan)->kode_lahan ?? '-'; $grade = $item->grade ?? 0;
        $panjang = $item->panjang ?? '-'; $jenis =
        optional($item->jenisKayu)->nama_kayu ?? '-'; return $kodeLahan . '|' .
        $grade . '|' . $panjang . '|' . $jenis; }); $grandBatang = 0; $grandM3 =
        0; $grandHarga = 0; @endphp @foreach($grouped as $key => $items) @php
        [$lahan, $grade, $ukuran, $jenis] = explode('|', $key); $gradeText =
        $grade == 1 ? 'A' : ($grade == 2 ? 'B' : '-'); $subtotalBatang =
        $items->sum('jumlah_batang'); $subtotalM3 = $items->sum('kubikasi');
        $subtotalHarga = $items->sum('total_harga'); $grandBatang +=
        $subtotalBatang; $grandM3 += $subtotalM3; $grandHarga += $subtotalHarga;
        @endphp

        <div class="group-title">
            {{ $lahan }}&nbsp;&nbsp;{{ $ukuran }} {{ $jenis }} ({{
                $gradeText
            }})
        </div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: center">D</th>
                    <th style="text-align: center">Q</th>
                    <th style="text-align: center">m³</th>
                    <th style="text-align: center">Harga</th>
                    <th style="text-align: center">Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $detail)
                <tr>
                    <td style="text-align: right">{{ $detail->diameter }}</td>
                    <td>{{ $detail->jumlah_batang }}</td>
                    <td>
                        {{ number_format($detail->kubikasi ?? 0, 4, ',', '.') }}
                    </td>
                    <td>
                        Rp.
                        {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}
                    </td>
                    <td>
                        Rp.
                        {{ number_format($detail->total_harga ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align: right">Total</th>
                    <th>{{ $subtotalBatang }}</th>
                    <th>{{ number_format($subtotalM3, 4, ",", ".") }} m³</th>
                    <th colspan="2" style="text-align: right">
                        Rp. {{ number_format($subtotalHarga, 0, ",", ".") }}
                    </th>
                </tr>
            </tfoot>
        </table>
        @endforeach

        <div style="margin-top: 20px; display: flex; justify-content: flex-end">
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
                        {{ number_format($totalKubikasi, 4, ",", ".") }} m³
                    </td>
                    <td style="text-align: right; border: 1px solid #000">
                        Grand Total
                    </td>
                    <td style="border: 1px solid #000">
                        Rp. {{ number_format($grandTotal, 0, ",", ".") }}
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
                        {{ number_format($totalBatang) }} Batang
                    </td>
                    <td></td>
                    <td style="padding: 4px 10px; border: 1px solid #000">
                        Rp. {{ number_format($selisih, 0, ",", ".") }}
                    </td>
                </tr>

                <!-- Baris Total Akhir yang full lebar -->
                <tr>
                    <td
                        colspan="4"
                        style="
                            text-align: right;
                            font-weight: bold;
                            font-size: 18px; /* ukuran font lebih besar */
                            padding: 10px 12px;
                            border: 2px solid #000; /* tebal agar menonjol */
                            background: #f2f2f2;
                        "
                    >
                        Total Akhir: Rp.
                        {{ number_format($hargaFinal, 0, ",", ".") }}
                    </td>
                </tr>
            </table>
        </div>
        <br /><br /><br />
        <table class="signature" style="width: 100%">
            <tr>
                <td>Penanggung Jawab Kayu</td>
                <td>Penerima</td>
            </tr>
            <tr>
                <td style="height: 70px"></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ $record->penanggung_jawab ?? '-' }}</td>
                <td>{{ $record->penerima ?? '-' }}</td>
            </tr>
        </table>
        <div class="footer">Dicetak pada: {{ now()->format('d-m-Y H:i') }}</div>

        <!-- <script>
            window.print(); // otomatis buka dialog print
        </script> -->
    </body>
</html>
