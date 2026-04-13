<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPelaksanaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'komentar',
        'id_indikator',
        'id_pelaksanaan',
        'edited_by'
    ];

    public function pelaksanaan() {
        return $this->belongsTo(Pelaksanaan::class, 'id_pelaksanaan');
    }
    public function links() {
        return $this->morphMany(Link::class, 'linkable');
    }
    public function buktiEvaluasi() {
        return $this->hasOne(BuktiEvaluasi::class, 'id_bukti_pelaksanaan');
    }
}
