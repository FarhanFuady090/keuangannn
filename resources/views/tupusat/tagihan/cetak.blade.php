<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Histori Tagihan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table, .table th, .table td { border: 1px solid #000; }
        .table th, .table td { padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h2>Histori Tagihan</h2>

    <p><strong>Nama Siswa:</strong> {{ $siswa->nama }}</p>
    <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
    <p><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Jenis Pembayaran</th>
                <th>Tahun Ajaran</th>
                <th>Bulan</th>
                <th>Nominal</th>
                <th>Jumlah Dibayar</th>
                <th>Status</th>
                <th>Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tagihans as $tagihan)
                <tr>
                    <td>{{ $tagihan->jenisPembayaran->nama_pembayaran }}</td>
                    <td>{{ $tagihan->tahunAjaran->tahun_ajaran }}</td>
                    <td>{{ $tagihan->bulan ?? '-' }}</td>
                    <td>{{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                    <td>{{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($tagihan->status) }}</td>
                    <td>{{ $tagihan->tanggal_bayar ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total Tagihan:</strong> Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
    <p><strong>Total Dibayar:</strong> Rp {{ number_format($totalDibayar, 0, ',', '.') }}</p>

    <br><br>
    <p style="text-align:right;">Tanggal Cetak: {{ now()->format('d-m-Y') }}</p>
</body>
</html>
