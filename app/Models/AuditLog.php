<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    // @dbml-sync:table-property
    protected $table = 'audit_log';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'user_id',
        'nama_aksi',
        'nama_tabel',
        'record_id',
        'waktu_perubahan',
        'keterangan',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'waktu_perubahan' => 'datetime',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // @enddbml-sync:relations
}
