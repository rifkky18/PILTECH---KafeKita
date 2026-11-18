<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    public $timestamps = false;

    // @dbml-sync:table-property
    protected $table = 'karyawan';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nik',
        'nama',
        'no_hp',
        'alamat',
        'tanggal_masuk',
        'jabatan_id',
        'status',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'tanggal_masuk' => 'date',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'karyawan_id', 'id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'karyawan_id', 'id');
    }

    public function cutis()
    {
        return $this->hasMany(Cuti::class, 'karyawan_id', 'id');
    }

    public function lemburs()
    {
        return $this->hasMany(Lembur::class, 'karyawan_id', 'id');
    }

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'karyawan_id', 'id');
    }

    // @enddbml-sync:relations
}
