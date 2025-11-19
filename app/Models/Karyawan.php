<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'jabatan_id',
        'foto',
        'status',
        'nomor_telepon',
        'tanggal_masuk',
    ];

    public function user()
    {
        // return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'user_id');
    }
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class, 'karyawan_id');
    }
}
