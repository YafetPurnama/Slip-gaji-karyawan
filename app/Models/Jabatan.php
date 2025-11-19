<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jabatan',
        'gaji_pokok',
        'tunjangan_transport',
        'uang_makan',
        'uang_bpjs',
        'uang_lembur',
    ];
    
    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'uang_makan' => 'decimal:2',
        'uang_bpjs' => 'decimal:2',
        'uang_lembur' => 'decimal:2',
    ];
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }
}