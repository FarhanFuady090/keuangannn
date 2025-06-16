<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tabungan Siswa</title>
    <style>
            body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 40px;
            background-color: #f9f9f9;
        }

        h3 {
            margin-bottom: 5px;
            font-size: 20px;
            color: #2c3e50;
        }

        h4 {
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 16px;
            color: #34495e;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        th {
            background-color: #f0f4f8;
            color: #2c3e50;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 2px solid #ccc;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .saldo {
            margin-top: 20px;
            background-color: #ecf0f1;
            padding: 10px;
            border-radius: 6px;
            width: fit-content;
        }

        .text-red-600 {
            color: #e74c3c;
            font-weight: bold;
        }

        .text-green-700 {
            color: #27ae60;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h3>Laporan Tabungan Siswa</h3>
    <p><strong>Nama:</strong> {{ $tabungan->siswa->nama }}</p>

    <div class="saldo">
        <p><strong>Saldo Awal:</strong> Rp {{ number_format($tabungan->saldo_awal, 0, ',', '.') }}</p>
        <p><strong>Saldo Akhir:</strong> Rp {{ number_format($tabungan->saldo_akhir, 0, ',', '.') }}</p>
    </div>

    <h4>Riwayat Transaksi</h4>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Saldo</th>
                <th>Petugas</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tabungan->transaksi as $trx)
                <tr>
                    <td>{{ $trx->created_at->translatedFormat('d F Y h:i A') }}</td>
                    <td>{{ $trx->jenis_transaksi }}</td>
                    <td class="{{ $trx->jenis_transaksi == 'Penarikan' ? 'text-red-600' : 'text-green-700' }}">
                        Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                    </td>
                    <td>Rp {{ number_format($trx->saldo_berjalan, 0, ',', '.') }}</td>
                    <td>{{ $trx->petugas }}</td>
                    <td>{{ $trx->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
