@if($detailGaji)
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">
      Slip Gaji Bulan: {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}
    </h6>
    <a href="{{ route('pegawai.gaji.print', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-sm btn-success"
      target="_blank">
      <i class="fas fa-print fa-sm"></i> Cetak Slip
    </a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-borderless" width="100%" cellspacing="0">
        <tr>
          <th width="30%">Gaji Pokok</th>
          <td class="text-right">Rp {{ number_format($detailGaji->gaji_pokok, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <th>Tunjangan Transport</th>
          <td class="text-right">Rp {{ number_format($detailGaji->tunjangan_transport, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <th>Uang Makan</th>
          <td class="text-right">Rp {{ number_format($detailGaji->uang_makan, 0, ',', '.') }}</td>
        </tr>
        <tr class="border-top">
          <th>Total Penerimaan</th>
          <th class="text-right">Rp {{ number_format($detailGaji->gaji_kotor, 0, ',', '.') }}</th>
        </tr>
        <tr>
          <th>Total Potongan</th>
          <td class="text-right text-danger">- Rp {{ number_format($detailGaji->total_potongan, 0, ',', '.') }}</td>
        </tr>
        <tr class="bg-light text-dark font-weight-bold border-top">
          <th class="h5">Gaji Bersih Diterima</th>
          <td class="text-right h5">Rp {{ number_format($detailGaji->gaji_bersih, 0, ',', '.') }}</td>
        </tr>
      </table>
    </div>
  </div>
@else
  <div class="card-body">
    <div class="alert alert-warning text-center">
      Data gaji untuk periode yang dipilih tidak ditemukan.
    </div>
  </div>
@endif