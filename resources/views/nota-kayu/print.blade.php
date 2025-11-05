<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Nota Kayu</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: black;
                background: white;
                margin: 40px;
            }
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th,
            td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
            th {
                background: #f2f2f2;
            }
            .footer {
                margin-top: 30px;
                text-align: right;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <h2>Nota Kayu</h2>

        <table>
            <tr>
                <th>No Nota</th>
                <td>{{ $record->no_nota }}</td>
            </tr>
            <tr>
                <th>Tanggal Kayu Masuk</th>
                <td>{{ $record->kayuMasuk->tgl_kayu_masuk }}</td>
            </tr>
            <tr>
                <th>Seri</th>
                <td>{{ $record->kayuMasuk->seri }}</td>
            </tr>
            <tr>
                <th>Supplier</th>
                <td>
                    {{ $record->kayuMasuk->penggunaanSupplier->nama_supplier ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>Penanggung Jawab</th>
                <td>{{ $record->penanggung_jawab }}</td>
            </tr>
            <tr>
                <th>Penerima</th>
                <td>{{ $record->penerima }}</td>
            </tr>
            <tr>
                <th>Satpam</th>
                <td>{{ $record->satpam }}</td>
            </tr>
        </table>
        <h3>Detail Kayu Masuk</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Kayu</th>
                    <th>Lahan</th>
                    <th>Diameter</th>
                    <th>Panjang</th>
                    <th>Grade</th>
                    <th>Jumlah Batang</th>
                    <th>Kubikasi</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->kayuMasuk->detailMasukanKayu as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $detail->jenisKayu->nama_kayu ?? '-' }}</td>
                    <td>{{ $detail->lahan->kode_lahan ?? '-' }}</td>
                    <td>{{ $detail->diameter }}</td>
                    <td>{{ $detail->panjang }}</td>
                    <td>{{ $detail->grade }}</td>
                    <td>{{ $detail->jumlah_batang }}</td>
                    <td>{{ number_format($detail->kubikasi, 6) }}</td>

                    {{-- Harga satuan dengan Rp --}}
                    <td>
                        Rp.
                        {{ number_format($detail->harga_satuan, 3, ',', '.') }}
                    </td>

                    {{-- Total harga juga dengan Rp --}}
                    <td>
                        Rp.
                        {{ number_format($detail->total_harga, 3, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php $grandTotal =
                $record->kayuMasuk->detailMasukanKayu->sum('total_harga');
                $totalKubikasi =
                $record->kayuMasuk->detailMasukanKayu->sum('kubikasi'); @endphp
                <tr>
                    <th colspan="7" style="text-align: right">Total</th>
                    <th>{{ number_format($totalKubikasi, 6, ",", ".") }} m³</th>
                    <th>Grand Total</th>
                    <th>Rp. {{ number_format($grandTotal, 3, ",", ".") }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="footer">Dicetak pada: {{ now()->format('d-m-Y H:i') }}</div>

        <script>
            window.print(); // otomatis buka dialog print
        </script>
    </body>
</html>
