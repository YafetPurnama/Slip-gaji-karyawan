<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'status_kehadiran',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}