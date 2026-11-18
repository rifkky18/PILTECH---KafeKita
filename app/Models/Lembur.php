<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    public $timestamps = false;

    // @dbml-sync:table-property
    protected $table = 'lembur';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jumlah_jam',
        'keterangan',
        'status',
        'approver_id',
        'tanggal_keputusan',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'tanggal' => 'date',
        'tanggal_keputusan' => 'datetime',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approver_id', 'id');
    }

    // @enddbml-sync:relations
}
