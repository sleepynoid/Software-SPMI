<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPengendalian extends Model
{
    use HasFactory;
    protected $fillable = [
        'temuan',
        'akar_masalah',
        'rtl',
        'pelaksanaan_rtl',
        'user_id',
        'id_bukti_evaluasi',
        'edited_by',
    ];

    public function buktiEvaluasi() {
        return $this->belongsTo(BuktiEvaluasi::class, 'id_bukti_evaluasi');
    }

    public function peningkatan() {
        return $this->hasOne(Peningkatan::class, 'id_pengendalian');
    }
}
