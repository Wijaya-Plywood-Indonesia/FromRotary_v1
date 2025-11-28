<!DOCTYPE html>
<html>
    <head>
        <title>Laporan Kayu Masuk</title>
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th,
            td {
                border: 1px solid #444;
                padding: 6px;
                text-align: center;
            }
            th {
                background: #f1f1f1;
            }
        </style>
    </head>

    <body>
        <a
            href="{{ route('laporan.kayu-masuk.export') }}"
            style="
                display: inline-block;
                padding: 8px 14px;
                background: #4caf50;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin-bottom: 15px;
            "
        >
            Export Excel
        </a>
        <h2>Laporan Kayu Masuk</h2>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Seri</th>
                    <th>Panjang</th>
                    <th>Jenis</th>
                    <th>Lahan</th>
                    <th>Banyak</th>
                    <th>M3</th>
                    <th>Poin</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->seri }}</td>
                    <td>{{ $row->panjang }}</td>
                    <td>{{ $row->jenis }}</td>
                    <td>{{ $row->lahan }}</td>

                    <td>{{ $row->banyak }}</td>

                    <td>{{ number_format($row->m3, 4) }}</td>

                    <td>{{ number_format($row->poin, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
