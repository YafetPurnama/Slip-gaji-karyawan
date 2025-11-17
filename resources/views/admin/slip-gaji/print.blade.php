<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $slipData->karyawan->nama_lengkap }} - {{ \Carbon\Carbon::create()->month((int)$slipData->bulan)->translatedFormat('F') }} {{ $slipData->tahun }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #333;
            background-color: #f4f7f6;
        }
        .container {
            width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header .company-info h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header .company-info p {
            margin: 0;
            color: #7f8c8d;
        }
        .header .slip-title h2 {
            margin: 0;
            text-align: right;
            font-size: 20px;
            color: #3498db;
        }
        .employee-details {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .employee-details table {
            width: 100%;
        }
        .employee-details td {
            padding: 5px 0;
            border: 0;
        }
        .employee-details td:first-child {
            width: 120px;
            font-weight: 500;
            color: #555;
        }
        .salary-summary {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        .salary-summary > div {
            width: 50%;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th {
            text-align: left;
            padding: 10px;
            background-color: #ecf0f1;
            border-bottom: 2px solid #bdc3c7;
            font-weight: 600;
        }
        .summary-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .summary-table td:last-child {
            text-align: right;
        }
        .text-danger { color: #e74c3c; }
        .total-row {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            color: #7f8c8d;
            font-size: 12px;
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .no-print button {
            padding: 10px 20px;
            border: none;
            background-color: #27ae60;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        @media print {
            body { background-color: #fff; }
            .container { margin: 0; box-shadow: none; border: 1px solid #eee;}
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                <h1>PT. Sejahtera Abadi</h1>
                <p>Laporan Rincian Gaji Karyawan</p>
            </div>
            <div class="slip-title">
                <h2>SLIP GAJI</h2>
                <p>Periode: {{ \Carbon\Carbon::create()->month((int)$slipData->bulan)->translatedFormat('F') }} {{ $slipData->tahun }}</p>
            </div>
        </div>

        <div class="employee-details">
            <table>
                <tr>
                    <td>Nama Karyawan</td>
                    <td>: <strong>{{ $slipData->karyawan->nama_lengkap }}</strong></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>: {{ $slipData->karyawan->nip }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: {{ $slipData->karyawan->jabatan->nama_jabatan ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="salary-summary">
            <div class="penerimaan">
                <table class="summary-table">
                    <thead><tr><th colspan="2">PENERIMAAN</th></tr></thead>
                    <tbody>
                        <tr><td>Gaji Pokok</td><td>Rp {{ number_format($slipData->gaji_pokok, 0, ',', '.') }}</td></tr>
                        <tr><td>Tunjangan Transport</td><td>Rp {{ number_format($slipData->tunjangan_transport, 0, ',', '.') }}</td></tr>
                        <tr><td>Uang Makan</td><td>Rp {{ number_format($slipData->uang_makan, 0, ',', '.') }}</td></tr>
                        <tr class="total-row"><td>Total Penerimaan</td><td>Rp {{ number_format($slipData->gaji_kotor, 0, ',', '.') }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="potongan">
                <table class="summary-table">
                    <thead><tr><th colspan="2">POTONGAN</th></tr></thead>
                    <tbody>
                        @if($slipData->potongan_alpha->total > 0)
                            <tr><td>Alpha ({{ $slipData->potongan_alpha->jumlah_hari }} hari)</td><td class="text-danger">- Rp {{ number_format($slipData->potongan_alpha->total, 0, ',', '.') }}</td></tr>
                        @endif
                        @foreach($slipData->rincian_potongan_lainnya as $potongan)
                            <tr><td>{{ $potongan->jenis_potongan }}</td><td class="text-danger">- Rp {{ number_format($potongan->jumlah, 0, ',', '.') }}</td></tr>
                        @endforeach
                        <tr class="total-row"><td>Total Potongan</td><td class="text-danger">- Rp {{ number_format($slipData->total_potongan, 0, ',', '.') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <table class="summary-table" style="margin-top: 30px;">
            <tr class="total-row" style="background-color: #3498db; color: white;">
                <td style="font-size: 1.2rem;">GAJI BERSIH DITERIMA</td>
                <td style="font-size: 1.2rem;">Rp {{ number_format($slipData->gaji_bersih, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>
    <div class="no-print">
        <button onclick="window.print()">Cetak Slip Gaji</button>
    </div>
</body>
</html>