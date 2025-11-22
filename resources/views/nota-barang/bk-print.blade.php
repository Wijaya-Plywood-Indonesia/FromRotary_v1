<!DOCTYPE html>
<html>
    <head>
        <title>Nota Barang Keluar</title>

        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                margin: 25px 40px;
            }

            h2,
            h3 {
                text-align: center;
                margin: 0;
                padding: 0;
            }

            .info-table,
            .detail-table,
            .signature-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 1px;
            }

            .info-table td {
                padding: 8px;
                /* border: 1px solid #000; */
            }

            .detail-table th,
            .detail-table td {
                border: 1px solid #000;
                padding: 6px 8px;
                text-align: left;
            }

            .detail-table th {
                background: #f0f0f0;
            }

            .signature-table td {
                text-align: center;
                padding-top: 20px;
            }

            @media print {
                body {
                    margin: 10mm 15mm;
                }
            }
        </style>
    </head>

    <body>
        <h2><strong>NOTA BARANG KELUAR</strong></h2>

        {{-- Informasi Nota --}}
        <table class="info-table">
            <tr>
                <td>
                    <strong>Tanggal</strong>:
                    {{ $record->tanggal->format('d-m-Y') }}
                </td>
                <td><strong>Kepada</strong>: {{ $record->tujuan_nota }}</td>
            </tr>
            <tr>
                <td><strong>No. Nota</strong>: {{ $record->no_nota }}</td>
                <td></td>
            </tr>
        </table>

        {{-- Detail Barang --}}
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center">No</th>
                    <th>Nama Barang</th>
                    <th style="width: 80px">Jumlah</th>
                    <th style="width: 80px">Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $item)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <br /><br />

        <table class="signature-table">
            <tr>
                <td style="width: 60%"></td>
                <td>Hormat Kami,</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <strong>{{ $record->pembuat->name ?? '-' }}</strong>
                </td>
            </tr>
        </table>

        <script>
            window.print();
        </script>
    </body>
</html>

<!-- <script>
            window.print();
        </script> -->
