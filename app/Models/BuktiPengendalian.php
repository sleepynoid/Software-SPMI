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
        'id_bukti_evaluasi'
    ];
}
