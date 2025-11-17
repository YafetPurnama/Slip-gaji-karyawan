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
    ];
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class);
    }
}