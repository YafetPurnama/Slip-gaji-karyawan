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
        'status_lembur',
    ];

    protected $casts = [
        'status_lembur' => 'string',
        'tanggal' => 'date',
    ];
    
    protected $attributes = [
        'status_lembur' => 'tidak',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}