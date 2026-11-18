<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;

    public $timestamps = false;

    // @dbml-sync:table-property
    protected $table = 'penggajian';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'karyawan_id',
        'periode',
        'gaji_pokok',
        'lembur',
        'potongan',
        'gaji_bersih',
        'status',
        'finalized_at',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'finalized_at' => 'datetime',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    // @enddbml-sync:relations
}
