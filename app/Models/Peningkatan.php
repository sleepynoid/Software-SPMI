<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peningkatan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_pengendalian',
        'komentar',
        'user_id',
        'edited_by',
    ];

    public function buktiPengendalian() {
        return $this->belongsTo(BuktiPengendalian::class, 'id_pengendalian');
    }
}
