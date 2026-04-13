<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_standar',
        'note',
    ];

    public function standar() {
        return $this->belongsTo(Standar::class, 'id_standar');
    }
    public function target() {
        return $this->hasOne(Target::class,'id_indikator');
    }
    public function buktiPelaksanaan() {
        return $this->hasOne(BuktiPelaksanaan::class, 'id_indikator');
    }
}
