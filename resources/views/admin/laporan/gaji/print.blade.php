<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Gaji Karyawan - {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}
  </title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

    /* CSS BARU UNTUK MENGATUR HALAMAN CETAK */
    @page {
      size: A4;
      margin: 1cm;

      @top-center {
        content: "";
      }

      @bottom-center {
        content: "";
      }
    }

    body {
      font-family: 'Poppins', sans-serif;
      font-size: 12px;
      color: #333;
    }

    .container {
      width: 100%;
      /* Ubah agar memenuhi area cetak */
      margin: auto;
    }

    .header {
      text-align: center;
      margin-bottom: 25px;
      border-bottom: 2px solid #333;
      padding-bottom: 10px;
    }

    .header h3,
    .header p {
      margin: 0;
    }

    .header h3 {
      font-size: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      font-weight: 600;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .signature {
      margin-top: 50px;
      width: 30%;
      float: right;
      text-align: center;
    }

    .signature .date {
      margin-bottom: 70px;
    }

    .no-print {
      text-align: center;
      margin: 20px;
    }

    .no-print button {
      padding: 10px 20px;
      border: none;
      background-color: #27ae60;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }

    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>

<body onload="window.print()">
  <div class="no-print">
    <button onclick="window.print()">Cetak Ulang</button>
  </div>
  <div class="container">
    <div class="header">
      <h3>LAPORAN GAJI KARYAWAN</h3>
      <p><strong>PT. SEJAHTERA ABADI</strong></p>
      <p>Periode: {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}</p>
    </div>

    <table>
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th>NIP</th>
          <th>Nama Karyawan</th>
          <th>Jabatan</th>
          <th class="text-right">Gaji Bersih</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($laporanGaji as $gaji)
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $gaji->karyawan->nip }}</td>
            <td>{{ $gaji->karyawan->nama_lengkap }}</td>
            <td>{{ $gaji->karyawan->jabatan->nama_jabatan ?? 'N/A' }}</td>
            <td class="text-right">Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">Tidak ada data gaji untuk periode ini.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="signature">
      <p class="date">Kota Tangerang Selatan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
      <p><strong>(___________________)</strong><br>Finance Manager</p>
    </div>
  </div>
</body>

</html>