<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    public $timestamps = false;

    // @dbml-sync:table-property
    protected $table = 'jabatan';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nama_jabatan',
        'gaji_pokok',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [

    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'jabatan_id', 'id');
    }

    // @enddbml-sync:relations
}
