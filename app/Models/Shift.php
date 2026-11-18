<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    public $timestamps = false;

    // @dbml-sync:table-property
    protected $table = 'shift';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'shift_id', 'id');
    }

    // @enddbml-sync:relations
}
