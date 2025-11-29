<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <title>Rekap Nota Barang Masuk</title>

        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #000;
            }

            h2 {
                text-align: center;
                margin-bottom: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 4px 6px;
                font-size: 11px;
            }

            th {
                background: #eee;
                text-align: center;
            }

            td {
                vertical-align: middle;
            }

            .center {
                text-align: center;
            }

            .right {
                text-align: right;
            }

            .print-btn {
                font-size: 11px;
                padding: 3px 6px;
                background: #ddd;
                border: 1px solid #888;
                text-decoration: none;
                color: black;
            }

            .print-btn:hover {
                background: #bbb;
            }

            @media print {
                .no-print {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <h2>REKAP NOTA BARANG MASUK</h2>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No Nota</th>
                    <th>Tujuan</th>
                    <th class="center">Total Item</th>
                    <th class="center">Total Qty</th>
                    <th class="no-print">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($data as $nota)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($nota->tanggal)->format('d-m-Y') }}
                    </td>
                    <td>{{ $nota->no_nota }}</td>
                    <td>{{ $nota->tujuan_nota }}</td>
                    <td class="center">{{ $nota->detail_count }}</td>
                    <td class="right">
                        {{ number_format($nota->detail_sum_jumlah ?? 0) }}
                    </td>

                    <td class="center no-print">
                        <a
                            class="print-btn"
                            target="_blank"
                            href="{{ route('nota-bm.print', $nota->id) }}"
                        >
                            PRINT
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="center">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
