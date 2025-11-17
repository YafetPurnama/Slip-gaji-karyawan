<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Slip Gaji Karyawan</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 14px;
    }

    .container {
      width: 80%;
      margin: auto;
    }

    .header,
    .footer {
      text-align: center;
      margin-bottom: 20px;
    }

    .header h2 {
      margin: 0;
    }

    .header p {
      margin: 5px 0;
    }

    .content table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .content th,
    .content td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    .content th {
      background-color: #f2f2f2;
      text-align: left;
    }

    .info-table td:first-child {
      width: 30%;
    }

    .salary-details td:first-child {
      width: 70%;
    }

    .signature {
      margin-top: 50px;
      overflow: hidden;
    }

    .signature div {
      width: 30%;
      float: right;
      text-align: center;
    }

    .signature p {
      margin-top: 60px;
    }

    @media print {
      body {
        -webkit-print-color-adjust: exact;
      }

      .no-print {
        display: none;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h2>PT. Sejahtera Abadi</h2>
      <p>Slip Gaji Karyawan</p>
      <p>Periode: {{ \Carbon\Carbon::create()->month((int) $slipData->bulan)->translatedFormat('F') }}
        {{ $slipData->tahun }}</p>
    </div>

    <div class="content">
      <table class="info-table">
        <tr>
          <td><strong>Nama Karyawan</strong></td>
          <td>: {{ $slipData->karyawan->nama_lengkap }}</td>
        </tr>
        <tr>
          <td><strong>NIP</strong></td>
          <td>: {{ $slipData->karyawan->nip }}</td>
        </tr>
        <tr>
          <td><strong>Jabatan</strong></td>
          <td>: {{ $slipData->karyawan->jabatan->nama_jabatan ?? 'N/A' }}</td>
        </tr>
      </table>

      <table class="salary-details">
        <tr>
          <th colspan="2">Penerimaan</th>
        </tr>
        <tr>
          <td>Gaji Pokok</td>
          <td style="text-align: right;">Rp {{ number_format($slipData->gaji_pokok, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td>Tunjangan Transport</td>
          <td style="text-align: right;">Rp {{ number_format($slipData->tunjangan_transport, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td>Uang Makan</td>
          <td style="text-align: right;">Rp {{ number_format($slipData->uang_makan, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <th colspan="2">Potongan</th>
        </tr>
        <tr>
          <td>Alpha ({{ $slipData->jumlah_alpha }} hari x Rp
            {{ number_format($slipData->potongan_alpha, 0, ',', '.') }})</td>
          <td style="text-align: right;">Rp {{ number_format($slipData->total_potongan, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <th>Total Gaji Diterima (Gaji Bersih)</th>
          <th style="text-align: right;">Rp {{ number_format($slipData->gaji_bersih, 0, ',', '.') }}</th>
        </tr>
      </table>
    </div>

    <div class="signature">
      <div>
        <p>Surakarta, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>(___________________)</strong><br>Finance</p>
      </div>
    </div>

    <button onclick="window.print()" class="no-print">Cetak</button>
  </div>
</body>

</html>