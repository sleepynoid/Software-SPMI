<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sheet extends Model {
    use HasFactory;

    protected $fillable = [
        'id_jurusan',
        'periode',
        'note',
        'tipe_sheet'
    ];

    public function jurusan() {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    public function penetapan() {
        return $this->hasOne(Penetapan::class, 'id_sheet');
    }

    public function pelaksanaan() {
        return $this->hasOne(Pelaksanaan::class, 'id_sheet');
    }

    public function evaluasi() {
        return $this->hasOne(Evaluasi::class, 'id_sheet');
    }
}
