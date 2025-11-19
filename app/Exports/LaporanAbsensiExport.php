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

class LaporanAbsensiExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $laporanAbsensi;
    protected $bulan;
    protected $tahun;

    public function __construct($laporanAbsensi, $bulan, $tahun)
    {
        $this->laporanAbsensi = $laporanAbsensi;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return collect($this->laporanAbsensi);
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama Karyawan',
            'Jabatan',
            'Hadir',
            'Sakit',
            'Izin',
            'Alpha',
            'Total Hari Kerja',
            'Persentase Kehadiran'
        ];
    }

    public function map($row): array
    {
        static $rowNumber = 1;

        $hadir = $row->jumlah_hadir ?? 0;
        $sakit = $row->jumlah_sakit ?? 0;
        $izin = 0; // Not currently tracked in ambilDataAbsensi
        $alpha = $row->jumlah_alpha ?? 0;
        $totalHariKerja = $hadir + $sakit + $izin + $alpha;

        // Calculate attendance percentage
        $persentase = $totalHariKerja > 0 ? (($hadir + $sakit) / $totalHariKerja) * 100 : 0;

        return [
            $rowNumber++,
            $row->nip ?? '',
            $row->nama_lengkap ?? '',
            $row->jabatan->nama_jabatan ?? '',
            $hadir,
            $sakit,
            $izin,
            $alpha,
            $totalHariKerja,
            round($persentase, 2) . '%'
        ];
    }

    public function title(): string
    {
        $bulanNama = Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F');
        return 'Laporan Absensi ' . $bulanNama . ' ' . $this->tahun;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:J1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAD3']]],
        ];
    }
}
