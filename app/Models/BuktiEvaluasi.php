<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiEvaluasi extends Model {
    use HasFactory;
    protected $fillable = [
        'adjustment',
        'komentar',
        'id_evaluasi',
        'id_bukti_pelaksanaan',
        'edited_by',
    ];
    public function evaluasi() {
        return $this->belongsTo(Evaluasi::class, 'id_evaluasi');
    }
    public function links() {
        return $this->morphMany(Link::class, 'linkable');
    }
    public function buktiPengendalian() {
        return $this->hasOne(BuktiPengendalian::class, 'id_bukti_evaluasi');
    }
}
