<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Absensi Karyawan</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 12px;
    }

    .container {
      width: 95%;
      margin: auto;
    }

    .header {
      text-align: center;
      margin-bottom: 25px;
    }

    .header h3,
    .header p {
      margin: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 7px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .text-center {
      text-align: center;
    }

    .signature {
      margin-top: 40px;
      overflow: hidden;
    }

    .signature div {
      width: 250px;
      float: right;
      text-align: center;
    }

    .signature .date {
      margin-bottom: 60px;
    }
  </style>
</head>

<body onload="window.print()">
  <div class="container">
    <div class="header">
      <h3>LAPORAN ABSENSI KARYAWAN</h3>
      <p>PT. SEJAHTERA ABADI</p>
      <p>Periode: {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}</p>
    </div>

    <table>
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th>Nama Karyawan</th>
          <th>Jabatan</th>
          <th class="text-center">Hadir</th>
          <th class="text-center">Sakit</th>
          <th class="text-center">Alpha</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($laporanAbsensi as $absensi)
      <tr>
        <td class="text-center">{{ $loop->iteration }}</td>
        <td>{{ $absensi->nama_lengkap }}</td>
        <td>{{ $absensi->jabatan }}</td>
        <td class="text-center">{{ $absensi->jumlah_hadir }}</td>
        <td class="text-center">{{ $absensi->jumlah_sakit }}</td>
        <td class="text-center">{{ $absensi->jumlah_alpha }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="text-center">Tidak ada data.</td>
      </tr>
    @endforelse
      </tbody>
    </table>

    <div class="signature">
      <div>
        <p class="date">Surakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br>
        <p><strong>(___________________)</strong><br>Personalia</p>
      </div>
    </div>
  </div>
</body>

</html>