<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 10px; }
        .wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .kwitansi {
            width: 48%;
            border: 1px solid #000;
            padding: 20px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .logo {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }
        .judul {
            font-weight: bold;
            font-size: 14px;
            text-align: left;
        }
        .info {
            margin: 5px 0;
        }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-style: italic;
        }
        .ttd {
            margin-top: 25px;
            text-align: right;
        }
        .ttd p {
            margin: 0;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
          <div class="header">
            <img src="{{ public_path('logo-yysn.png') }}" class="logo" alt="Logo">
              <div class="judul">
                KWITANSI PEMBAYARAN<br>
                <small>Yayasan Nurul Huda</small>
              </div>
          </div>

        <div class="info">
            <p>Nama Siswa: <strong>{{ $siswa->nama }}</strong></p>
            <p>NIS: {{ $siswa->nis }}</p>
            <p>Jenis Pembayaran: {{ $jenisPembayaran->nama_pembayaran }} ({{ $jenisPembayaran->type }})</p>
            <p>Tahun Ajaran: {{ $tahunAjaran->tahun_ajaran }}</p>
            <p>Bulan/Semester: {{ $tagihan->bulan ?? '-' }}</p>
            <p>Tanggal Bayar: {{ \Carbon\Carbon::parse($tagihan->tanggal_bayar)->format('d-m-Y') }}</p>
            <p>Nominal: Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
            <p>Jumlah Dibayar: Rp {{ number_format($tagihan->jumlah_dibayar, 0, ',', '.') }}</p>
            <p>Status: {{ ucfirst($tagihan->status) }}</p>
            <div class="ttd">
                    <p>Petugas</p>
                    <br><br>
                    <p>______________________</p>
                </div>
        </div>

        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
