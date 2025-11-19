<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanGajiExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $laporanGaji;
    protected $bulan;
    protected $tahun;

    public function __construct($laporanGaji, $bulan, $tahun)
    {
        $this->laporanGaji = $laporanGaji;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return collect($this->laporanGaji);
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama Karyawan',
            'Jabatan',
            'Gaji Pokok',
            'Tunjangan Transport',
            'Uang Makan',
            'Uang BPJS',
            'Total Lembur',
            'Potongan Alpha',
            'Potongan Lainnya',
            'Total Gaji Bersih',
        ];
    }

    public function map($row): array
    {
        static $rowNumber = 1;

        $gajiPokok = $row->karyawan->jabatan->gaji_pokok ?? 0;
        $tunjanganTransport = $row->karyawan->jabatan->tunjangan_transport ?? 0;
        $uangMakan = $row->karyawan->jabatan->uang_makan ?? 0;
        $uangBpjs = $row->karyawan->jabatan->uang_bpjs ?? 0;
        $totalLembur = $row->total_lembur ?? 0;
        $potonganAlpha = $row->potongan_alpha ?? 0;
        $potonganLainnya = $row->potongan_lainnya ?? 0;
        $gajiBersih = $row->gaji_bersih ?? 0;

        $data = [
            $rowNumber++, // This will auto-increment for each row
            $row->karyawan->nip ?? '',
            $row->karyawan->nama_lengkap ?? '',
            $row->karyawan->jabatan->nama_jabatan ?? '',
            $gajiPokok,
            $tunjanganTransport,
            $uangMakan,
            $uangBpjs,
            $totalLembur,
            $potonganAlpha,
            $potonganLainnya,
            $gajiBersih,
        ];

        return $data;
    }

    public function title(): string
    {
        $bulanNama = Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F');
        return 'Laporan Gaji ' . $bulanNama . ' ' . $this->tahun;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            // Styling a specific cell by coordinate.
            'A1:L1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAD3']]],
        ];
    }
}
